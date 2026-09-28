<?php

declare(strict_types=1);

namespace App;

use App\Controller\Admin\SurveyController as AdminSurveyController;
use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Controller\ParticipationController;
use App\Controller\PaymentController;
use App\Controller\ProfileController;
use App\Controller\QuestionController;
use App\Controller\ResultsController;
use App\Controller\RewardController;
use App\Controller\SurveyController;
use App\Http\AuthenticationRequired;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Http\Session;
use App\Payment\MidtransGateway;
use App\Payment\PaymentGateway;
use App\Repository\PaymentRepository;
use App\Repository\QuestionRepository;
use App\Repository\ResponseRepository;
use App\Repository\RewardRepository;
use App\Repository\SurveyRepository;
use App\Repository\UserRepository;
use App\Security\Auth;
use App\Security\Csrf;
use App\Security\LoginThrottle;
use App\Security\PasswordHasher;
use App\Security\SecurityHeaders;
use App\Security\SurveyPolicy;
use App\Service\PaymentService;
use App\Service\ResponseService;
use App\Service\SurveyPricing;
use App\Support\Config;
use App\Support\Database;
use App\Support\Env;
use App\Support\Url;
use App\View\View;
use ErrorException;
use PDO;
use Throwable;

/**
 * Boots configuration, wires dependencies and turns a request into a response.
 * Wiring is explicit on purpose: every object graph is visible in one file.
 */
final class App
{
    private const WEBHOOK_PATH = '/payments/midtrans/notification';

    /** @var array<string, object> */
    private array $instances = [];

    private function __construct(
        public readonly Config $config,
        private readonly string $rootDir,
    ) {
    }

    public static function boot(string $rootDir): self
    {
        Env::load($rootDir . '/.env');
        $config = Config::fromEnvironment();

        date_default_timezone_set($config->timezone);
        error_reporting(E_ALL);
        ini_set('display_errors', $config->debug ? '1' : '0');
        ini_set('log_errors', '1');

        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if ((error_reporting() & $severity) === 0) {
                return false;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        Url::configure($config);

        return new self($config, $rootDir);
    }

    public function handle(Request $request): Response
    {
        $isWebhook = $request->path === self::WEBHOOK_PATH;

        try {
            if (!$isWebhook) {
                $this->session()->start($this->config);
                $this->view()->share('currentPath', $request->path);
            }

            $response = $this->dispatch($request, $isWebhook);
        } catch (AuthenticationRequired) {
            $response = $this->redirectToLogin($request);
        } catch (HttpException $e) {
            $response = $this->errorResponse($e->status, $e->title ?? 'Error', $e->getMessage(), $isWebhook);
        } catch (Throwable $e) {
            error_log(sprintf('%s %s failed: %s', $request->method, $request->path, $e));
            $response = $this->errorResponse(500, 'Server error', 'Something went wrong on our side. Please try again later.', $isWebhook, $e);
        }

        return (new SecurityHeaders($this->config))->apply($response);
    }

    private function dispatch(Request $request, bool $isWebhook): Response
    {
        $route = $this->router()->match($request->method, $request->path);

        if (!$isWebhook) {
            if ($request->isPost() && !$this->csrf()->isValid($request->input(Csrf::FIELD))) {
                throw HttpException::csrfMismatch();
            }

            if ($request->path === '/admin' || str_starts_with($request->path, '/admin/')) {
                $user = $this->auth()->user() ?? throw new AuthenticationRequired();
                if (!$user->isAdmin()) {
                    throw HttpException::forbidden();
                }
            }
        }

        [$class, $method] = $route['handler'];

        return $this->controller($class)->{$method}($request, ...$route['params']);
    }

    private function redirectToLogin(Request $request): Response
    {
        if ($request->method === 'GET') {
            $this->session()->set('intended_url', Url::to($request->fullPath()));
        }

        $this->session()->flash('info', 'Please sign in to continue.');

        return Response::redirect(Url::to('/login'));
    }

    private function errorResponse(int $status, string $title, string $message, bool $asJson, ?Throwable $exception = null): Response
    {
        if ($asJson) {
            return Response::json(['message' => $message], $status);
        }

        try {
            return Response::html($this->view()->render('errors/error', [
                'status' => $status,
                'title' => $title,
                'message' => $message,
                'exception' => $this->config->debug ? $exception : null,
            ]), $status);
        } catch (Throwable $renderError) {
            error_log('Error page failed to render: ' . $renderError);

            return new Response('Internal Server Error', 500, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
    }

    private function controller(string $class): object
    {
        $base = [$this->view(), $this->auth(), $this->session()];

        return match ($class) {
            HomeController::class => new HomeController(...$base, surveys: $this->surveys()),
            AuthController::class => new AuthController(...$base, users: $this->users(), hasher: $this->hasher(), throttle: $this->throttle()),
            ProfileController::class => new ProfileController(...$base, users: $this->users(), hasher: $this->hasher(), csrf: $this->csrf()),
            RewardController::class => new RewardController(...$base, rewards: $this->rewards()),
            ParticipationController::class => new ParticipationController(
                ...$base,
                surveys: $this->surveys(),
                questions: $this->questions(),
                responses: $this->responses(),
                responseService: $this->responseService(),
            ),
            SurveyController::class => new SurveyController(
                ...$base,
                surveys: $this->surveys(),
                questions: $this->questions(),
                payments: $this->payments(),
                pricing: $this->pricing(),
                policy: $this->policy(),
            ),
            QuestionController::class => new QuestionController(...$base, questions: $this->questions(), policy: $this->policy()),
            PaymentController::class => new PaymentController(
                ...$base,
                payments: $this->paymentService(),
                gateway: $this->gateway(),
                policy: $this->policy(),
            ),
            ResultsController::class => new ResultsController(
                ...$base,
                questions: $this->questions(),
                responses: $this->responses(),
                policy: $this->policy(),
            ),
            AdminSurveyController::class => new AdminSurveyController(...$base, surveys: $this->surveys()),
            default => throw new \LogicException(sprintf('No wiring for controller %s.', $class)),
        };
    }

    public function database(): PDO
    {
        return $this->shared(PDO::class, fn (): PDO => Database::connect($this->config));
    }

    public function users(): UserRepository
    {
        return $this->shared(UserRepository::class, fn () => new UserRepository($this->database()));
    }

    public function paymentService(): PaymentService
    {
        return $this->shared(PaymentService::class, fn () => new PaymentService(
            $this->database(),
            $this->surveys(),
            $this->questions(),
            $this->payments(),
            $this->gateway(),
            $this->pricing(),
        ));
    }

    private function router(): Router
    {
        return $this->shared(Router::class, function (): Router {
            $router = new Router();
            (require $this->rootDir . '/config/routes.php')($router);

            return $router;
        });
    }

    private function session(): Session
    {
        return $this->shared(Session::class, fn () => new Session());
    }

    private function csrf(): Csrf
    {
        return $this->shared(Csrf::class, fn () => new Csrf($this->session()));
    }

    private function auth(): Auth
    {
        return $this->shared(Auth::class, fn () => new Auth($this->session(), $this->csrf(), $this->users(), $this->hasher()));
    }

    private function hasher(): PasswordHasher
    {
        return $this->shared(PasswordHasher::class, fn () => new PasswordHasher());
    }

    private function throttle(): LoginThrottle
    {
        return $this->shared(LoginThrottle::class, fn () => new LoginThrottle($this->database()));
    }

    private function view(): View
    {
        return $this->shared(View::class, function (): View {
            $view = new View($this->rootDir . '/templates');
            $view->share('auth', $this->auth());
            $view->share('csrf', $this->csrf());
            $view->share('session', $this->session());

            return $view;
        });
    }

    private function surveys(): SurveyRepository
    {
        return $this->shared(SurveyRepository::class, fn () => new SurveyRepository($this->database()));
    }

    private function questions(): QuestionRepository
    {
        return $this->shared(QuestionRepository::class, fn () => new QuestionRepository($this->database()));
    }

    private function responses(): ResponseRepository
    {
        return $this->shared(ResponseRepository::class, fn () => new ResponseRepository($this->database()));
    }

    private function rewards(): RewardRepository
    {
        return $this->shared(RewardRepository::class, fn () => new RewardRepository($this->database()));
    }

    private function payments(): PaymentRepository
    {
        return $this->shared(PaymentRepository::class, fn () => new PaymentRepository($this->database()));
    }

    private function pricing(): SurveyPricing
    {
        return $this->shared(SurveyPricing::class, fn () => new SurveyPricing($this->config->platformFee));
    }

    private function policy(): SurveyPolicy
    {
        return $this->shared(SurveyPolicy::class, fn () => new SurveyPolicy($this->surveys()));
    }

    private function gateway(): PaymentGateway
    {
        return $this->shared(PaymentGateway::class, fn () => new MidtransGateway(
            $this->config->midtransServerKey,
            $this->config->midtransProduction,
        ));
    }

    private function responseService(): ResponseService
    {
        return $this->shared(ResponseService::class, fn () => new ResponseService(
            $this->database(),
            $this->surveys(),
            $this->questions(),
            $this->responses(),
            $this->rewards(),
        ));
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @param callable(): T $factory
     * @return T
     */
    private function shared(string $id, callable $factory): object
    {
        return $this->instances[$id] ??= $factory();
    }
}

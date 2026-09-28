<?php

declare(strict_types=1);

namespace App\View;

use RuntimeException;

/**
 * Plain PHP templates. Every template must escape output with e(); the only
 * raw output allowed is the already-rendered $content inside the layout.
 */
final class View
{
    /** @var array<string, mixed> */
    private array $shared = [];

    /** @var array<string, string> */
    private array $slots = [];

    public function __construct(private readonly string $templateDir)
    {
    }

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    /**
     * Lets a page template pass small values (title, flags) up to the layout.
     */
    public function set(string $slot, string $value): void
    {
        $this->slots[$slot] = $value;
    }

    public function get(string $slot, string $default = ''): string
    {
        return $this->slots[$slot] ?? $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        $content = $this->renderFile($template, $data);

        if ($layout === null) {
            return $content;
        }

        return $this->renderFile($layout, [...$data, 'content' => $content]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function renderFile(string $template, array $data): string
    {
        $path = $this->templateDir . '/' . $template . '.php';
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Template "%s" not found.', $template));
        }

        $vars = [...$this->shared, ...$data, 'view' => $this];

        ob_start();
        try {
            (static function (string $__path, array $__vars): void {
                extract($__vars, EXTR_SKIP);
                require $__path;
            })($path, $vars);
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string) ob_get_clean();
    }
}

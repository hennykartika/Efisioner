<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Domain\Survey;
use App\Domain\User;
use App\Repository\SurveyRepository;
use App\Repository\UserRepository;
use App\Support\Config;
use App\Support\Database;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Runs against a real MySQL/MariaDB database named by TEST_DB_* variables.
 * The schema is rebuilt once per run and every table is emptied before each
 * test. Tests are skipped when no database is reachable.
 */
abstract class DatabaseTestCase extends TestCase
{
    private static ?PDO $pdo = null;
    private static ?string $unavailable = null;

    protected PDO $db;

    public static function setUpBeforeClass(): void
    {
        if (self::$pdo !== null || self::$unavailable !== null) {
            return;
        }

        $database = getenv('TEST_DB_DATABASE') ?: 'efisioner_test';
        if (!str_ends_with($database, '_test')) {
            self::$unavailable = sprintf('refusing to wipe "%s"; the test database name must end with _test', $database);

            return;
        }

        $config = new Config(
            env: 'testing',
            debug: false,
            url: 'http://localhost',
            timezone: date_default_timezone_get(),
            dbHost: getenv('TEST_DB_HOST') ?: '127.0.0.1',
            dbPort: (int) (getenv('TEST_DB_PORT') ?: 3306),
            dbName: $database,
            dbUser: getenv('TEST_DB_USERNAME') ?: 'root',
            dbPassword: getenv('TEST_DB_PASSWORD') ?: '',
            midtransServerKey: '',
            midtransProduction: false,
            platformFee: 1000,
        );

        try {
            $pdo = Database::connect($config);
        } catch (PDOException $e) {
            self::$unavailable = $e->getMessage();

            return;
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
            $pdo->exec('DROP TABLE `' . str_replace('`', '', (string) $table) . '`');
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        $schema = (string) file_get_contents(dirname(__DIR__, 2) . '/database/schema.sql');
        $schema = (string) preg_replace('/^--.*$/m', '', $schema);
        foreach (array_filter(array_map('trim', explode(';', $schema))) as $statement) {
            $pdo->exec($statement);
        }

        self::$pdo = $pdo;
    }

    protected function setUp(): void
    {
        if (self::$pdo === null) {
            $this->markTestSkipped('Test database not available: ' . self::$unavailable);
        }

        $this->db = self::$pdo;
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
            $this->db->exec('TRUNCATE TABLE `' . $table . '`');
        }
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function createUser(string $username): User
    {
        $users = new UserRepository($this->db);
        $id = $users->create($username, password_hash('secret-password', PASSWORD_BCRYPT), [
            'full_name' => ucfirst($username) . ' Tester',
            'email' => $username . '@example.test',
            'phone' => null,
            'address' => null,
            'birth_date' => '1998-03-10',
            'gender' => 'female',
            'education' => 'bachelor',
            'occupation' => 'student',
            'occupation_other' => null,
            'institution' => 'Test University',
        ]);

        return $users->find($id) ?? throw new \RuntimeException('User not created.');
    }

    protected function createSurvey(User $owner, int $target = 2, int $reward = 5000, int $questions = 1): Survey
    {
        $surveys = new SurveyRepository($this->db);
        $id = $surveys->create($owner->id, 'Sleep habits', 'A short study about sleep.', $target, $reward);

        $insert = $this->db->prepare('INSERT INTO questions (survey_id, body, option_a, option_b) VALUES (?, ?, ?, ?)');
        for ($i = 1; $i <= $questions; $i++) {
            $insert->execute([$id, "Question {$i}?", 'Yes', 'No']);
        }

        return $surveys->find($id) ?? throw new \RuntimeException('Survey not created.');
    }

    protected function publish(Survey $survey): Survey
    {
        $surveys = new SurveyRepository($this->db);
        $surveys->activate($survey->id, $survey->targetRespondents, $survey->rewardPerRespondent);

        return $surveys->find($survey->id) ?? throw new \RuntimeException('Survey vanished.');
    }

    protected function scalar(string $sql, array $params = []): mixed
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchColumn();
    }
}

<?php

declare(strict_types=1);

// Grants or revokes the admin role. Usage: php bin/make-admin.php <username> [--revoke]

use App\App;
use App\Domain\Role;

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require dirname(__DIR__) . '/vendor/autoload.php';

$username = mb_strtolower(trim($argv[1] ?? ''));
$revoke = in_array('--revoke', $argv, true);

if ($username === '' || str_starts_with($username, '--')) {
    fwrite(STDERR, "Usage: php bin/make-admin.php <username> [--revoke]\n");
    exit(1);
}

$app = App::boot(dirname(__DIR__));

if (!$app->users()->setRole($username, $revoke ? Role::User : Role::Admin)) {
    fwrite(STDERR, "User \"{$username}\" was not found.\n");
    exit(1);
}

echo $revoke ? "\"{$username}\" is no longer an administrator.\n" : "\"{$username}\" is now an administrator.\n";

<?php

declare(strict_types=1);

// Publishes a survey whose payment was confirmed outside Midtrans, for example
// a bank transfer checked by hand. Also useful on a local machine without
// Midtrans sandbox keys.
//
// Usage: php bin/mark-survey-paid.php <survey-id> <reference>

use App\App;
use App\Domain\RuleViolation;

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require dirname(__DIR__) . '/vendor/autoload.php';

$surveyId = filter_var($argv[1] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$reference = trim($argv[2] ?? '');

if ($surveyId === false || $reference === '' || mb_strlen($reference) > 64) {
    fwrite(STDERR, "Usage: php bin/mark-survey-paid.php <survey-id> <reference (max 64 chars)>\n");
    exit(1);
}

$app = App::boot(dirname(__DIR__));

try {
    $app->paymentService()->recordManualPayment($surveyId, $reference);
} catch (RuleViolation $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

echo "Survey {$surveyId} is paid and live.\n";

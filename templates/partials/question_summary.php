<?php
/**
 * @var int $number
 * @var array{question: App\Domain\Question, counts: array<string, int>, total: int} $item
 * @var bool|null $withChart
 */
$withChart ??= false;
$question = $item['question'];
$options = $question->options();
?>
<div class="question-summary mb-4">
    <h3 class="h6"><?= e($number) ?>. <?= e($question->body) ?></h3>
    <div class="row g-3 align-items-center">
        <div class="<?= $withChart ? 'col-md-7' : 'col-12' ?>">
            <table class="table table-sm table-bordered mb-0">
                <thead>
                <tr>
                    <th scope="col">Option</th>
                    <th scope="col" class="text-end">Responses</th>
                    <th scope="col" class="text-end">Share</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($options as $letter => $text): ?>
                    <?php $count = $item['counts'][$letter] ?? 0; ?>
                    <tr>
                        <td><?= e($letter) ?>. <?= e($text) ?></td>
                        <td class="text-end"><?= e($count) ?></td>
                        <td class="text-end"><?= e($item['total'] > 0 ? number_format($count / $item['total'] * 100, 1) . '%' : '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($withChart && $item['total'] > 0): ?>
            <div class="col-md-5">
                <div class="chart-box">
                    <canvas data-chart="<?= json_attr([
                        'labels' => array_map(static fn (string $letter, string $text): string => $letter . '. ' . $text, array_keys($options), $options),
                        'values' => array_values($item['counts']),
                    ]) ?>" role="img" aria-label="Chart of answers for question <?= e($number) ?>"></canvas>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

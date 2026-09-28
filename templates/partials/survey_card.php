<?php
/**
 * @var array<string, mixed> $survey row from SurveyRepository::listOpen()
 */
?>
<div class="col">
    <article class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h3 class="h5 card-title"><?= e($survey['title']) ?></h3>
            <p class="small text-body-secondary mb-2">by <?= e($survey['owner_name']) ?></p>
            <p class="card-text text-clamp-3 flex-grow-1"><?= e($survey['description']) ?></p>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="fw-semibold text-success"><?= e(rupiah((int) $survey['reward_per_respondent'])) ?></span>
                <span class="small text-body-secondary"><?= e((string) $survey['remaining_slots']) ?> spots left</span>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
            <a class="btn btn-brand w-100" href="<?= e(url('/surveys/' . $survey['id'])) ?>">Answer this survey</a>
        </div>
    </article>
</div>

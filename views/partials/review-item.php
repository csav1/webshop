<?php
/**
 * Review Item Partial
 * @var array $review
 * @var int|null $currentUserId
 */

use Core\Auth;

$currentUserId = Auth::id();
?>
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
            <?php if (!empty($review['user_avatar'])): ?>
                <img src="<?= e($review['user_avatar']) ?>" alt="" class="w-10 h-10 rounded-full">
            <?php else: ?>
                <div class="w-10 h-10 bg-nba-blue text-white rounded-full flex items-center justify-center font-bold">
                    <?= strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>

            <div>
                <p class="font-semibold text-gray-800">
                    <?= e($review['user_name'] ?? 'Anonym') ?>
                </p>
                <p class="text-sm text-gray-500">
                    <?= formatDate($review['created_at'] ?? date('Y-m-d')) ?>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <?= formatStars((float)($review['rating'] ?? 0)) ?>
        </div>
    </div>

    <?php if (!empty($review['verified_purchase'])): ?>
        <div class="mb-3">
            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                <i class="fas fa-check-circle"></i> Verifizierter Kauf
            </span>
        </div>
    <?php endif; ?>

    <p class="text-gray-700 mb-4 leading-relaxed">
        <?= nl2br(e($review['content'] ?? '')) ?>
    </p>

    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <form action="<?= url('/bewertungen/hilfreich') ?>" method="post" class="inline">
            <?= \Core\View::csrf() ?>
            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
            <?php if ($currentUserId): ?>
                <button type="submit" class="text-gray-500 hover:text-nba-blue transition-colors text-sm">
                    <i class="far fa-thumbs-up mr-1"></i> Hilfreich
                    <?php if (($review['helpful_count'] ?? 0) > 0): ?>
                        <span class="font-medium">(
                            <?= $review['helpful_count'] ?>)
                        </span>
                    <?php endif; ?>
                </button>
            <?php else: ?>
                <span class="text-gray-400 text-sm">
                    <i class="far fa-thumbs-up mr-1"></i>
                    <?= $review['helpful_count'] ?? 0 ?> fanden das hilfreich
                </span>
            <?php endif; ?>
        </form>
    </div>
</div>
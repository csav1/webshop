<?php
/**
 * Product Card Partial
 * @var array $product
 */
?>
<div class="bg-white rounded-2xl overflow-hidden card-hover shadow-md group">
    <a href="<?= url('/produkt/' . e($product['slug'])) ?>" class="block">
        <div class="aspect-square bg-gray-100 relative overflow-hidden">
            <img src="<?= $product['image'] ? asset($product['image']) : asset('images/placeholder.jpg') ?>" alt="<?= e($product['name']) ?>"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                loading="lazy">

            <?php if (($product['stock'] ?? 0) < 10 && ($product['stock'] ?? 0) > 0): ?>
                <span class="absolute top-4 left-4 bg-orange-500 text-white text-xs px-3 py-1 rounded-full font-medium">
                    Nur noch
                    <?= $product['stock'] ?> Stück
                </span>
            <?php elseif (($product['stock'] ?? 0) === 0): ?>
                <span class="absolute top-4 left-4 bg-gray-500 text-white text-xs px-3 py-1 rounded-full font-medium">
                    Ausverkauft
                </span>
            <?php endif; ?>

            <?php if (isset($product['avg_rating']) && $product['avg_rating'] >= 4.5): ?>
                <span class="absolute top-4 right-4 bg-yellow-400 text-gray-900 text-xs px-3 py-1 rounded-full font-bold">
                    ⭐ Bestseller
                </span>
            <?php endif; ?>
        </div>
    </a>

    <div class="p-5">
        <a href="<?= url('/produkt/' . e($product['slug'])) ?>">
            <h3 class="font-bold text-lg text-gray-800 mb-2 hover:text-nba-red transition-colors line-clamp-2">
                <?= e($product['name']) ?>
            </h3>
        </a>

        <?php if (isset($product['category_name'])): ?>
            <p class="text-gray-500 text-sm mb-2">
                <i class="fas fa-tag mr-1"></i>
                <?= e($product['category_name']) ?>
            </p>
        <?php endif; ?>

        <div class="flex items-center gap-2 mb-3">
            <?php
            $rating = $product['avg_rating'] ?? $product['rating'] ?? 0;
            $reviews = $product['review_count'] ?? 0;
            ?>
            <span class="text-sm">
                <?= formatStars($rating) ?>
            </span>
            <span class="text-gray-400 text-sm">
                (
                <?= number_format($rating, 1) ?>)
                <?php if ($reviews > 0): ?>
                    <span class="ml-1">
                        <?= $reviews ?> Bewertungen
                    </span>
                <?php endif; ?>
            </span>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-nba-red">
                <?= formatPrice($product['price']) ?>
            </span>

            <form action="<?= url('/warenkorb/hinzufuegen') ?>" method="post">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                    class="bg-nba-blue hover:bg-blue-900 text-white p-3 rounded-full transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                    <?= ($product['stock'] ?? 0) === 0 ? 'disabled' : '' ?>>
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </form>
        </div>
    </div>
</div>
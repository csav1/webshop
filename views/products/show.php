<?php
/**
 * Product Detail Page
 * @var array $product
 * @var array $reviews
 * @var array $reviewStats
 * @var array $relatedProducts
 */

use Core\Auth;
use Core\Session;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="<?= url('/') ?>" class="hover:text-nba-red">Startseite</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="<?= url('/produkte') ?>" class="hover:text-nba-red">Produkte</a></li>
            <?php if (!empty($product['category_name'])): ?>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="<?= url('/produkte?kategorie=' . e($product['category_slug'])) ?>" class="hover:text-nba-red">
                        <?= e($product['category_name']) ?>
                    </a></li>
            <?php endif; ?>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800 font-medium">
                <?= e($product['name']) ?>
            </li>
        </ol>
    </nav>

    <!-- Product Info -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
            <!-- Image -->
            <div class="aspect-square bg-gray-100 relative">
                <img src="<?= $product['image'] ? asset($product['image']) : asset('images/placeholder.jpg') ?>"
                    alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
                <?php if ($product['stock'] === 0): ?>
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                        <span class="bg-white text-gray-800 px-6 py-3 rounded-full font-bold text-lg">Ausverkauft</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Details -->
            <div class="p-8 lg:p-12 flex flex-col">
                <span class="text-nba-blue font-medium mb-2">
                    <i class="fas fa-tag mr-1"></i>
                    <?= e($product['category_name'] ?? 'Produkt') ?>
                </span>

                <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
                    <?= e($product['name']) ?>
                </h1>

                <!-- Rating -->
                <div class="flex items-center gap-4 mb-6">
                    <?= formatStars($reviewStats['avg_rating'] ?? 0) ?>
                    <span class="text-gray-500">
                        <?= $reviewStats['total_reviews'] ?? 0 ?> Bewertungen
                    </span>
                </div>

                <!-- Price -->
                <div class="text-4xl font-bold text-nba-red mb-6">
                    <?= formatPrice($product['price']) ?>
                </div>

                <!-- Description -->
                <p class="text-gray-600 leading-relaxed mb-8 flex-grow">
                    <?= nl2br(e($product['description'])) ?>
                </p>

                <!-- Stock Status -->
                <div class="mb-6">
                    <?php if ($product['stock'] > 10): ?>
                        <span class="inline-flex items-center gap-2 text-green-600">
                            <i class="fas fa-check-circle"></i> Auf Lager
                        </span>
                    <?php elseif ($product['stock'] > 0): ?>
                        <span class="inline-flex items-center gap-2 text-orange-600">
                            <i class="fas fa-exclamation-circle"></i> Nur noch
                            <?= $product['stock'] ?> Stück verfügbar
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-2 text-red-600">
                            <i class="fas fa-times-circle"></i> Nicht verfügbar
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Add to Cart -->
                <form action="<?= url('/warenkorb/hinzufuegen') ?>" method="post" class="flex gap-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="flex items-center border border-gray-300 rounded-lg">
                        <button type="button"
                            onclick="this.nextElementSibling.stepDown(); this.nextElementSibling.dispatchEvent(new Event('change'))"
                            class="px-4 py-3 text-gray-600 hover:bg-gray-100">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>"
                            class="w-16 text-center border-0 outline-none">
                        <button type="button"
                            onclick="this.previousElementSibling.stepUp(); this.previousElementSibling.dispatchEvent(new Event('change'))"
                            class="px-4 py-3 text-gray-600 hover:bg-gray-100">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button type="submit"
                        class="flex-1 bg-nba-red hover:bg-red-700 text-white py-4 rounded-lg font-bold text-lg transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                        <?= $product['stock'] === 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-shopping-cart mr-2"></i> In den Warenkorb
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <section class="mb-12" id="bewertungen">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-star text-yellow-400 mr-2"></i> Kundenbewertungen
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Review Stats -->
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="text-center mb-4">
                    <div class="text-5xl font-bold text-gray-800">
                        <?= number_format($reviewStats['avg_rating'] ?? 0, 1) ?>
                    </div>
                    <div class="mt-2">
                        <?= formatStars($reviewStats['avg_rating'] ?? 0) ?>
                    </div>
                    <p class="text-gray-500 mt-2">
                        <?= $reviewStats['total_reviews'] ?? 0 ?> Bewertungen
                    </p>
                </div>
            </div>

            <!-- Review Form & List -->
            <div class="lg:col-span-2 space-y-6">
                <?php if (Auth::check()): ?>
                    <!-- Add Review Form -->
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <h3 class="font-bold text-lg mb-4">Ihre Bewertung</h3>
                        <form action="<?= url('/bewertungen') ?>" method="post">
                            <?= \Core\View::csrf() ?>
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                            <!-- Star Rating -->
                            <div class="mb-4" x-data="{ rating: 5 }">
                                <label class="block text-gray-700 font-medium mb-2">Bewertung</label>
                                <div class="flex gap-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" @click="rating = <?= $i ?>"
                                            :class="rating >= <?= $i ?> ? 'text-yellow-400' : 'text-gray-300'"
                                            class="text-3xl transition-colors hover:scale-110">
                                            ★
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="rating" :value="rating">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Ihre Meinung</label>
                                <textarea name="content" rows="4" required
                                    placeholder="Teilen Sie Ihre Erfahrung mit diesem Produkt..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-nba-red/50"></textarea>
                            </div>

                            <button type="submit"
                                class="bg-nba-blue hover:bg-blue-900 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i> Bewertung abschicken
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="bg-blue-50 rounded-xl p-6 text-center">
                        <p class="text-gray-700 mb-4">Melden Sie sich an, um eine Bewertung zu schreiben.</p>
                        <a href="<?= url('/anmelden') ?>"
                            class="inline-flex items-center gap-2 bg-nba-blue text-white px-6 py-2 rounded-full hover:bg-blue-900 transition-colors">
                            <i class="fas fa-sign-in-alt"></i> Anmelden
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Sort Options -->
                <?php if (!empty($reviews)): ?>
                    <div class="flex items-center justify-between bg-white rounded-xl p-4 shadow-sm">
                        <span class="text-gray-600 text-sm font-medium">Sortieren nach:</span>
                        <select onchange="window.location.href='?sort=' + this.value + '#bewertungen'" 
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-nba-red/50">
                            <option value="helpful" <?= ($reviewSort ?? 'helpful') === 'helpful' ? 'selected' : '' ?>>Hilfreichste</option>
                            <option value="newest" <?= ($reviewSort ?? '') === 'newest' ? 'selected' : '' ?>>Neueste zuerst</option>
                            <option value="oldest" <?= ($reviewSort ?? '') === 'oldest' ? 'selected' : '' ?>>Älteste zuerst</option>
                            <option value="highest" <?= ($reviewSort ?? '') === 'highest' ? 'selected' : '' ?>>Beste Bewertung</option>
                            <option value="lowest" <?= ($reviewSort ?? '') === 'lowest' ? 'selected' : '' ?>>Schlechteste Bewertung</option>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Review List -->
                <?php if (empty($reviews)): ?>
                    <div class="bg-white rounded-xl p-8 text-center shadow-sm">
                        <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Noch keine Bewertungen. Seien Sie der Erste!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($reviews as $review): ?>
                            <?php Core\View::partial('review-item', ['review' => $review]); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-basketball text-nba-red mr-2"></i> Ähnliche Produkte
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($relatedProducts as $relProduct): ?>
                    <?php Core\View::partial('product-card', ['product' => $relProduct]); ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</div>

<!-- Structured Data -->
<?= product_jsonld($product) ?>
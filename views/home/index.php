<?php
/**
 * Homepage
 * @var array $featuredProducts
 * @var array $categories
 */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Hero Section -->
    <section class="relative overflow-hidden rounded-3xl nba-gradient text-white p-8 md:p-16 mb-12">
        <div class="relative z-10">
            <span class="inline-block bg-white/20 text-white px-4 py-1 rounded-full text-sm font-medium mb-4">
                🏆 Offizielle NBA Merchandise
            </span>
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
                Willkommen beim Fake NBA Store
            </h1>
            <p class="text-xl md:text-2xl text-white/80 mb-8 max-w-2xl">
                Entdecken Sie original NBA Merchandise - Trikots, Caps, Basketbälle und Sneakers Ihrer Lieblingsteams!
            </p>
            <a href="<?= url('/produkte') ?>"
                class="inline-flex items-center gap-2 bg-white text-nba-red px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition-all hover:scale-105 shadow-lg">
                <i class="fas fa-basketball"></i> Jetzt einkaufen
            </a>
        </div>
        <div class="absolute right-0 top-0 w-1/2 h-full opacity-20 hidden md:block">
            <div class="absolute w-64 h-64 bg-white/30 rounded-full -top-20 -right-20"></div>
            <div class="absolute w-48 h-48 bg-white/20 rounded-full bottom-10 right-40"></div>
            <div class="absolute w-32 h-32 bg-white/10 rounded-full top-1/3 right-20"></div>
        </div>
    </section>

    <!-- Categories -->
    <section class="mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-tags text-nba-red mr-2"></i> Kategorien
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <?php
            $icons = [
                'trikots' => 'fas fa-tshirt',
                'caps-muetzen' => 'fas fa-hat-cowboy', // Alternative für Caps
                'sneaker' => 'fas fa-shoe-prints',
                'basketbaelle' => 'fas fa-basketball-ball',
                'hoodies-jacken' => 'fas fa-vest',
                'accessoires' => 'fas fa-shopping-bag'
            ];
            foreach ($categories as $cat):
                ?>
                <a href="<?= url('/kategorie/' . e($cat['slug'])) ?>"
                    class="bg-white rounded-2xl p-6 text-center card-hover shadow-md group">
                    <span class="text-4xl mb-3 block group-hover:scale-110 transition-transform text-nba-blue group-hover:text-nba-red">
                        <i class="<?= $icons[$cat['slug']] ?? 'fas fa-box' ?>"></i>
                    </span>
                    <span class="font-semibold text-gray-800 group-hover:text-nba-red transition-colors">
                        <?= e($cat['name']) ?>
                    </span>
                    <?php if (isset($cat['product_count'])): ?>
                        <span class="block text-sm text-gray-500 mt-1">
                            <?= $cat['product_count'] ?> Produkte
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-fire text-orange-500 mr-2"></i> Beliebte Produkte
            </h2>
            <a href="<?= url('/produkte') ?>" class="text-nba-blue hover:text-blue-900 font-medium flex items-center gap-2">
                Alle anzeigen <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredProducts as $product): ?>
                <?php Core\View::partial('product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Features -->
    <section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div
                class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-truck text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-2">Kostenloser Versand</h3>
            <p class="text-gray-500 text-sm">Ab 50€ Bestellwert</p>
        </div>
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-undo text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-2">30 Tage Rückgabe</h3>
            <p class="text-gray-500 text-sm">Kostenlose Retouren</p>
        </div>
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div
                class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-2">Original Produkte</h3>
            <p class="text-gray-500 text-sm">100% Authentisch</p>
        </div>
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div
                class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-2">24/7 Support</h3>
            <p class="text-gray-500 text-sm">Immer für Sie da</p>
        </div>
    </section>

</div>
<?php
/**
 * Cart Page
 * @var array $items
 * @var float $total
 */

use Core\Auth;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-shopping-cart text-nba-blue mr-2"></i> Warenkorb
        </h1>
        <?php if (!empty($items)): ?>
            <form action="<?= url('/warenkorb/leeren') ?>" method="post">
                <button type="submit" class="text-red-500 hover:text-red-700 font-medium flex items-center gap-2">
                    <i class="fas fa-trash-alt"></i> Alle entfernen
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="bg-white rounded-xl p-12 text-center shadow-sm">
            <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Ihr Warenkorb ist leer</h2>
            <p class="text-gray-500 mb-6">Fügen Sie Produkte hinzu, um mit dem Einkauf zu beginnen.</p>
            <a href="<?= url('/produkte') ?>"
                class="inline-flex items-center gap-2 bg-nba-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors">
                <i class="fas fa-basketball"></i> Weiter einkaufen
            </a>
        </div>
    <?php else: ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($items as $item): ?>
                    <div class="bg-white rounded-xl p-4 shadow-sm flex gap-4">
                        <a href="<?= url('/produkt/' . e($item['slug'])) ?>" class="flex-shrink-0">
                            <img src="<?= !empty($item['image']) ? asset($item['image']) : asset('images/placeholder.jpg') ?>"
                                alt="<?= e($item['name']) ?>" class="w-24 h-24 object-cover rounded-lg">
                        </a>
                        <div class="flex-grow">
                            <a href="<?= url('/produkt/' . e($item['slug'])) ?>"
                                class="font-bold text-gray-800 hover:text-nba-red transition-colors">
                                <?= e($item['name']) ?>
                            </a>
                            <p class="text-nba-red font-bold text-lg mt-1">
                                <?= formatPrice($item['price']) ?>
                            </p>

                            <div class="flex items-center gap-4 mt-3">
                                <form action="<?= url('/warenkorb/aktualisieren') ?>" method="post" class="flex items-center gap-2">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                        <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>"
                                            class="px-3 py-2 text-gray-600 hover:bg-gray-100 <?= $item['quantity'] <= 1 ? 'opacity-50' : '' ?>">
                                            <i class="fas fa-minus text-sm"></i>
                                        </button>
                                        <span class="w-12 text-center font-medium">
                                            <?= $item['quantity'] ?>
                                        </span>
                                        <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>"
                                            class="px-3 py-2 text-gray-600 hover:bg-gray-100">
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </div>
                                </form>

                                <form action="<?= url('/warenkorb/entfernen') ?>" method="post">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">
                                <?= formatPrice($item['price'] * $item['quantity']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl p-6 shadow-sm sticky top-24">
                    <h2 class="font-bold text-xl mb-4">Zusammenfassung</h2>

                    <div class="space-y-3 pb-4 border-b border-gray-200">
                        <div class="flex justify-between text-gray-600">
                            <span>Zwischensumme</span>
                            <span>
                                <?= formatPrice($subtotal) ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Versand</span>
                            <span>
                                <?= $subtotal >= 50 ? 'Kostenlos' : formatPrice($shipping) ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between font-bold text-xl py-4">
                        <span>Gesamt</span>
                        <span class="text-nba-red">
                            <?= formatPrice($total) ?>
                        </span>
                    </div>
                    
                    <p class="text-xs text-gray-400 mb-2">inkl. MwSt.</p>

                    <?php if ($subtotal < 50): ?>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fas fa-truck mr-1"></i> Noch
                            <?= formatPrice(50 - $subtotal) ?> bis zum kostenlosen Versand!
                        </p>
                    <?php endif; ?>

                    <?php if (Auth::check()): ?>
                        <a href="<?= url('/kasse') ?>"
                            class="block w-full bg-nba-red hover:bg-red-700 text-white py-4 rounded-lg font-bold text-center transition-colors">
                            <i class="fas fa-lock mr-2"></i> Zur Kasse
                        </a>
                    <?php else: ?>
                        <a href="<?= url('/anmelden?redirect=/kasse') ?>"
                            class="block w-full bg-nba-blue hover:bg-blue-800 text-white py-4 rounded-lg font-bold text-center transition-colors">
                            <i class="fas fa-user mr-2"></i> Zur Kasse (Anmelden)
                        </a>
                    <?php endif; ?>

                    <a href="<?= url('/produkte') ?>" class="block w-full text-center text-nba-blue hover:text-blue-900 mt-4 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i> Weiter einkaufen
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>
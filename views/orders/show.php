<?php
/**
 * Order Details Page
 * @var array $order
 */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Back Link -->
    <a href="<?= url('/bestellungen') ?>" class="inline-flex items-center text-gray-500 hover:text-nba-red font-medium mb-8 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Zurück zur Übersicht
    </a>

    <!-- Order Header -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 border border-gray-100">
        <div class="nba-gradient p-8 md:p-10 text-white relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 flex items-center gap-3">
                        Bestellung #<?= e($order['order_number']) ?>
                    </h1>
                    <p class="text-white/80 flex items-center gap-2">
                        <i class="far fa-calendar-alt"></i> 
                        Bestellt am <?= formatDateTime($order['created_at']) ?>
                    </p>
                </div>
                <div class="flex flex-col items-end gap-2">
                     <span class="px-4 py-2 rounded-full bg-white/20 backdrop-blur-md text-white font-bold border border-white/30 shadow-sm">
                        <?= formatOrderStatus($order['status']) ?>
                     </span>
                     <span class="text-sm text-white/70">
                         Zahlung: <?= e(ucfirst($order['payment_method'])) ?> 
                         (<?= formatPaymentStatus($order['payment_status']) ?>)
                     </span>
                </div>
            </div>
            
            <!-- Decorative Background -->
            <div class="absolute right-0 top-0 w-1/2 h-full opacity-10 pointer-events-none">
                <i class="fas fa-file-invoice-dollar text-[12rem] absolute -top-10 -right-10 transform -rotate-12"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-nba-red"></i> Bestellpositionen
                    </h2>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="p-6 flex gap-6 hover:bg-gray-50 transition-colors">
                            <a href="<?= url('/produkt/' . e($item['product_slug'] ?? '')) ?>" class="shrink-0 w-24 h-24 bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                                <?php if (!empty($item['product_image'])): ?>
                                    <img src="<?= e(asset($item['product_image'])) ?>" alt="<?= e($item['product_name']) ?>"
                                    class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <i class="fas fa-image text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <a href="<?= url('/produkt/' . e($item['product_slug'] ?? '')) ?>" class="text-lg font-bold text-gray-800 hover:text-nba-blue">
                                        <?= e($item['product_name']) ?>
                                    </a>
                                    <span class="font-bold text-gray-900">
                                        <?= formatPrice($item['total']) ?>
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500 mb-1">
                                    Einzelpreis: <?= formatPrice($item['price_at_purchase']) ?>
                                </div>
                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-gray-100 text-gray-700 text-sm font-medium">
                                    Menge: <?= $item['quantity'] ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Info & Summary -->
        <div class="space-y-6">
            
            <!-- Shipping Address -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-nba-blue"></i> Lieferadresse
                </h2>
                <address class="not-italic text-gray-600 space-y-1">
                    <p class="font-bold text-gray-900"><?= e($order['shipping_name']) ?></p>
                    <p><?= e($order['shipping_street']) ?></p>
                    <p><?= e($order['shipping_zip']) ?> <?= e($order['shipping_city']) ?></p>
                    <p><?= e($order['shipping_country']) ?></p>
                    <?php if (!empty($order['shipping_phone'])): ?>
                        <p class="mt-2 text-sm"><i class="fas fa-phone mr-1"></i> <?= e($order['shipping_phone']) ?></p>
                    <?php endif; ?>
                </address>
            </div>

            <!-- Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-calculator text-gray-400"></i> Zusammenfassung
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Zwischensumme</span>
                        <span><?= formatPrice($order['subtotal']) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Versandkosten</span>
                        <span><?= formatPrice($order['shipping_cost']) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>MwSt. (19%)</span>
                        <span><?= formatPrice($order['tax']) ?></span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                        <span class="font-bold text-gray-900 text-lg">Gesamtsumme</span>
                        <span class="font-bold text-2xl text-nba-red"><?= formatPrice($order['total']) ?></span>
                    </div>
                </div>
            </div>





        </div>
    </div>
</div>

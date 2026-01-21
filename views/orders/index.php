<?php
/**
 * Order History Page
 * @var array $orders
 */
?>

<?php
/**
 * Order History Page
 * @var array $orders
 */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header Section with Gradient -->
    <div class="rounded-3xl nba-gradient text-white p-8 md:p-12 mb-12 shadow-2xl relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-3xl md:text-5xl font-bold mb-4 flex items-center gap-4">
                <i class="fas fa-history opacity-80"></i> Meine Bestellungen
            </h1>
            <p class="text-lg md:text-xl text-white/90 max-w-2xl">
                Hier finden Sie eine Übersicht aller Ihrer Einkäufe.
            </p>
        </div>
        <!-- Decorative Elements -->
        <div class="absolute right-0 top-0 w-1/3 h-full opacity-10 pointer-events-none">
            <i class="fas fa-basketball-ball text-9xl absolute -top-10 -right-10 transform rotate-12"></i>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-2xl p-16 text-center shadow-lg border border-gray-100 max-w-2xl mx-auto">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-bag text-4xl text-gray-300"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-3">Noch keine Bestellungen</h2>
            <p class="text-gray-500 mb-8">Starten Sie Ihre Sammlung mit den neuesten NBA Styles.</p>
            <a href="<?= url('/produkte') ?>"
                class="inline-flex items-center gap-3 bg-nba-red text-white px-8 py-4 rounded-full font-bold hover:bg-red-700 transition-all hover:scale-105 shadow-md">
                <i class="fas fa-search"></i> Jetzt Produkte entdecken
            </a>
        </div>
    <?php else: ?>

        <div class="grid gap-8">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group">
                    <!-- Order Header -->
                    <div class="bg-gray-50/50 p-6 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-nba-blue">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Bestellnummer</p>
                                <p class="font-bold text-gray-900 font-mono"><?= e($order['order_number']) ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm text-gray-500">Bestelldatum</p>
                                <p class="font-medium text-gray-900"><?= formatDateTime($order['created_at']) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Summe</p>
                                <p class="font-bold text-xl text-nba-red"><?= formatPrice($order['total']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Content -->
                    <div class="p-6 md:p-8">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                            
                            <!-- Product Previews -->
                            <div class="flex-1">
                                <div class="flex flex-wrap gap-4">
                                    <?php foreach (array_slice($order['items'] ?? [], 0, 4) as $item): ?>
                                        <a href="<?= url('/produkt/' . e($item['product_slug'])) ?>" 
                                           class="relative w-20 h-20 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:border-nba-blue transition-colors group/item"
                                           title="<?= e($item['product_name']) ?>">
                                            <img src="<?= e(asset($item['product_image'])) ?>" 
                                                 alt="<?= e($item['product_name']) ?>"
                                                 class="w-full h-full object-cover group-hover/item:scale-110 transition-transform duration-500">
                                            <span class="absolute bottom-0 right-0 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded-tl-md font-medium">
                                                x<?= $item['quantity'] ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($order['items'] ?? []) > 4): ?>
                                        <div class="w-20 h-20 bg-gray-50 rounded-lg border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 hover:bg-gray-100 transition-colors cursor-pointer">
                                            <span class="font-bold text-lg">+<?= count($order['items']) - 4 ?></span>
                                            <span class="text-[10px]">weitere</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="w-full md:w-auto flex flex-col items-end gap-3 min-w-[200px]">
                                <div class="w-full p-4 bg-gray-50 rounded-xl mb-2 flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Status</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold shadow-sm <?= orderStatusColor($order['status']) ?>">
                                        <?= formatOrderStatus($order['status']) ?>
                                    </span>
                                </div>
                                <a href="<?= url('/bestellungen/' . e($order['order_number'])) ?>" 
                                   class="w-full text-center px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 font-bold rounded-xl hover:border-nba-blue hover:text-nba-blue transition-all group-hover:shadow-md">
                                    Details ansehen <i class="fas fa-arrow-right ml-2 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>
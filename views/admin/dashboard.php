<?php
/**
 * Admin Dashboard
 * @var array $stats
 */
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-chart-line text-nba-blue mr-2"></i> Admin Dashboard
        </h1>
        <span class="text-gray-500">Willkommen,
            <?= e(\Core\Auth::user()['name'] ?? 'Admin') ?>
        </span>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Benutzer</p>
                    <p class="text-2xl font-bold">
                        <?= $stats['total_users'] ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-basketball text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Produkte</p>
                    <p class="text-2xl font-bold">
                        <?= $stats['total_products'] ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Bestellungen</p>
                    <p class="text-2xl font-bold">
                        <?= $stats['total_orders'] ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-euro-sign text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Umsatz</p>
                    <p class="text-2xl font-bold">
                        <?= formatPrice($stats['total_revenue']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <a href="<?= url('/admin/produkte') ?>"
            class="bg-nba-blue hover:bg-blue-900 text-white p-4 rounded-xl flex items-center gap-3 transition-colors">
            <i class="fas fa-basketball text-2xl"></i>
            <span class="font-medium">Produkte verwalten</span>
        </a>
        <a href="<?= url('/admin/kategorien') ?>"
            class="bg-nba-blue hover:bg-blue-900 text-white p-4 rounded-xl flex items-center gap-3 transition-colors">
            <i class="fas fa-tags text-2xl"></i>
            <span class="font-medium">Kategorien</span>
        </a>
        <a href="<?= url('/admin/bestellungen') ?>"
            class="bg-nba-blue hover:bg-blue-900 text-white p-4 rounded-xl flex items-center gap-3 transition-colors">
            <i class="fas fa-box text-2xl"></i>
            <span class="font-medium">Bestellungen</span>
        </a>
        <a href="<?= url('/admin/benutzer') ?>"
            class="bg-nba-blue hover:bg-blue-900 text-white p-4 rounded-xl flex items-center gap-3 transition-colors">
            <i class="fas fa-users text-2xl"></i>
            <span class="font-medium">Benutzer</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-xl">Letzte Bestellungen</h2>
            </div>
            <div class="p-6">
                <?php if (empty($stats['recent_orders'])): ?>
                    <p class="text-gray-500">Noch keine Bestellungen.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($stats['recent_orders'] as $order): ?>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">
                                        <?= e($order['order_number']) ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <?= e($order['user_name'] ?? 'Gast') ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-nba-red">
                                        <?= formatPrice($order['total']) ?>
                                    </p>
                                    <span class="text-xs px-2 py-1 rounded-full <?= orderStatusColor($order['status']) ?>">
                                        <?= formatOrderStatus($order['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-xl">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i> Niedriger Lagerbestand
                </h2>
            </div>
            <div class="p-6">
                <?php if (empty($stats['low_stock'])): ?>
                    <p class="text-gray-500">Alle Produkte sind gut bevorratet.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($stats['low_stock'] as $product): ?>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">
                                        <?= e($product['name']) ?>
                                    </p>
                                </div>
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                                    <?= $product['stock'] ?> Stück
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
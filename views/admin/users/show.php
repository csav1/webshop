<?php
/**
 * Admin User Detail View
 * @var array $user
 * @var array $orders
 */
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= url('/admin/benutzer') ?>" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-800 transition-colors bg-white">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <?= e($user['name']) ?>
            </h1>
            <p class="text-gray-500 text-sm">
                Registriert seit <?= formatDateTime($user['created_at']) ?>
            </p>
        </div>
        <div class="ml-auto">
            <a href="<?= url('/admin/benutzer/' . $user['id'] . '/bearbeiten') ?>" class="bg-nba-blue hover:bg-blue-900 text-white px-6 py-2 rounded-xl font-bold transition-colors shadow-lg shadow-blue-900/20">
                Bearbeiten
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- User Stats / Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-24 h-24 rounded-full bg-nba-blue text-white flex items-center justify-center text-3xl font-bold">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">E-Mail</label>
                        <div class="font-medium text-gray-800"><?= e($user['email']) ?></div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Rolle</label>
                        <div class="font-medium text-gray-800">
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="text-purple-600 font-bold"><i class="fas fa-shield-alt mr-1"></i> Administrator</span>
                            <?php else: ?>
                                <span class="text-gray-600">Standard-Benutzer</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                        <div>
                            <?php if ($user['is_active']): ?>
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-bold">Aktiv</span>
                            <?php else: ?>
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs font-bold">Gesperrt</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Statistiken</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-nba-blue"><?= $user['total_orders'] ?? 0 ?></div>
                        <div class="text-xs text-blue-600 font-medium uppercase">Bestellungen</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600"><?= formatPrice($user['total_spend'] ?? 0) ?></div>
                        <div class="text-xs text-green-700 font-medium uppercase">Umsatz</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="font-bold text-xl text-gray-800">Letzte Bestellungen</h2>
            
            <?php if (empty($orders)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                    <i class="fas fa-shopping-basket text-4xl text-gray-200 mb-3"></i>
                    <p>Keine Bestellungen vorhanden.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider font-semibold border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Nr.</th>
                                    <th class="px-6 py-4">Datum</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Summe</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-mono text-sm"><?= e($order['order_number']) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500"><?= formatDateTime($order['created_at']) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs px-2 py-1 rounded-full <?= orderStatusColor($order['status']) ?>">
                                                <?= formatOrderStatus($order['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900"><?= formatPrice($order['total']) ?></td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="<?= url('/admin/bestellungen/' . $order['id']) ?>" class="text-nba-blue hover:text-blue-900 font-medium text-sm">
                                                Ansehen
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

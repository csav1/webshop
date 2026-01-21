<?php
/**
 * Admin Orders Index
 * @var array $orders
 * @var array $pagination
 * @var string|null $currentStatus
 */

$statuses = [
    'pending' => 'Offen',
    'processing' => 'In Bearbeitung',
    'shipped' => 'Versendet',
    'delivered' => 'Geliefert',
    'cancelled' => 'Storniert'
];
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-box text-nba-blue"></i> Bestellungen verwalten
    </h1>
</div>

<!-- Filters -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="<?= url('/admin/bestellungen') ?>" 
       class="px-4 py-2 rounded-full text-sm font-medium transition-colors border <?= !$currentStatus ? 'bg-nba-blue text-white border-nba-blue' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' ?>">
       Alle
    </a>
    <?php foreach ($statuses as $key => $label): ?>
        <a href="?status=<?= $key ?>" 
           class="px-4 py-2 rounded-full text-sm font-medium transition-colors border <?= $currentStatus === $key ? 'bg-nba-blue text-white border-nba-blue' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' ?>">
           <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold">Bestellung</th>
                    <th class="px-6 py-4 font-semibold">Kunde</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold">Bezahlung</th>
                    <th class="px-6 py-4 font-semibold">Summe</th>
                    <th class="px-6 py-4 font-semibold">Datum</th>
                    <th class="px-6 py-4 font-semibold text-right">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-search text-4xl text-gray-300"></i>
                                <span class="font-medium">Keine Bestellungen gefunden.</span>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 font-mono font-medium text-gray-900">
                                <?= e($order['order_number']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?= e($order['shipping_name']) ?></div>
                                <div class="text-xs text-gray-500">ID: <?= $order['user_id'] ?? 'Gast' ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap <?= orderStatusColor($order['status']) ?>">
                                    <?= formatOrderStatus($order['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm"><?= ucfirst($order['payment_method']) ?></span>
                                    <span class="text-xs <?= $order['payment_status'] === 'paid' ? 'text-green-600 font-bold' : 'text-gray-500' ?>">
                                        <?= formatPaymentStatus($order['payment_status']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= formatPrice($order['total']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= formatDateTime($order['created_at']) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= url('/admin/bestellungen/' . $order['id']) ?>" 
                                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-nba-blue text-white hover:bg-blue-900 text-sm font-medium transition-colors shadow-sm">
                                    Details <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['totalPages'] > 1): ?>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-center">
            <div class="flex items-center gap-1">
                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>" 
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                              <?= $i === $pagination['currentPage'] 
                                  ? 'bg-nba-blue text-white shadow-sm' 
                                  : 'text-gray-600 hover:bg-white hover:shadow-sm' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

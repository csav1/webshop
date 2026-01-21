<?php
/**
 * Admin Order Details
 * @var array $order
 */
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= url('/admin/bestellungen') ?>" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-800 transition-colors bg-white">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                Bestellung #<?= e($order['order_number']) ?>
            </h1>
            <p class="text-gray-500 text-sm">
                Bestellt am <?= formatDateTime($order['created_at']) ?> von 
                <span class="font-medium text-gray-900"><?= e($order['shipping_name']) ?></span>
            </p>
        </div>
        <div class="ml-auto flex gap-3">
            <span class="px-4 py-2 rounded-full font-bold shadow-sm <?= orderStatusColor($order['status']) ?>">
                Status: <?= formatOrderStatus($order['status']) ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Items -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-bold text-lg text-gray-800">Positionen</h2>
                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold"><?= count($order['items']) ?> Artikel</span>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="p-6 flex gap-4 items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 shrink-0">
                                <?php if (!empty($item['product_image'])): ?>
                                    <img src="<?= e(asset($item['product_image'])) ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow">
                                <div class="font-bold text-gray-800"><?= e($item['product_name']) ?></div>
                                <div class="text-sm text-gray-500">Artikel-Nr: <?= $item['product_id'] ?></div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-900"><?= formatPrice($item['total']) ?></div>
                                <div class="text-xs text-gray-500"><?= $item['quantity'] ?> x <?= formatPrice($item['price_at_purchase']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="bg-gray-50 p-6 space-y-2 border-t border-gray-100">
                    <div class="flex justify-between text-gray-600 text-sm">
                        <span>Zwischensumme</span>
                        <span><?= formatPrice($order['subtotal']) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600 text-sm">
                        <span>Versandkosten</span>
                        <span><?= formatPrice($order['shipping_cost']) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600 text-sm">
                        <span>MwSt. (19%)</span>
                        <span><?= formatPrice($order['tax']) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-900 font-bold text-lg pt-2 border-t border-gray-200">
                        <span>Gesamtsumme</span>
                        <span><?= formatPrice($order['total']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Management Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status Update -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-truck text-nba-blue"></i> Bestellstatus ändern
                    </h3>
                    <form action="<?= url('/admin/bestellungen/' . $order['id'] . '/status') ?>" method="post">
                        <?= \Core\View::csrf() ?>
                        <div class="mb-4">
                            <select name="status" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-2 focus:ring-blue-500/20 outline-none">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Offen</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>In Bearbeitung</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Versendet</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Geliefert</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Storniert</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-nba-blue hover:bg-blue-900 text-white font-bold py-2 rounded-lg transition-colors">
                            Status aktualisieren
                        </button>
                    </form>
                </div>

                <!-- Payment Status -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-credit-card text-green-600"></i> Zahlungsstatus
                    </h3>
                    <form action="<?= url('/admin/bestellungen/' . $order['id'] . '/zahlung') ?>" method="post">
                        <?= \Core\View::csrf() ?>
                        <div class="mb-4">
                            <select name="payment_status" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-2 focus:ring-blue-500/20 outline-none">
                                <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Ausstehend</option>
                                <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Bezahlt</option>
                                <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Fehlgeschlagen</option>
                                <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Erstattet</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 rounded-lg transition-colors">
                            Status speichern
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            
            <!-- Customer -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Kunde</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-nba-blue flex items-center justify-center font-bold">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <?php if ($order['user_id']): ?>
                            <a href="<?= url('/admin/benutzer/' . $order['user_id']) ?>" class="font-bold text-nba-blue hover:underline">
                                <?= e($order['shipping_name']) ?>
                            </a>
                        <?php else: ?>
                            <span class="font-bold text-gray-800"><?= e($order['shipping_name']) ?></span>
                            <span class="text-xs text-gray-500 ml-2">(Gast)</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Lieferadresse</h4>
                <address class="not-italic text-sm text-gray-600 space-y-1">
                    <p><?= e($order['shipping_street']) ?></p>
                    <p><?= e($order['shipping_zip']) ?> <?= e($order['shipping_city']) ?></p>
                    <p><?= e($order['shipping_country']) ?></p>
                    <?php if ($order['shipping_phone']): ?>
                        <p class="mt-2"><i class="fas fa-phone mr-1"></i> <?= e($order['shipping_phone']) ?></p>
                    <?php endif; ?>
                </address>
                
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mt-6 mb-2">Zahlungsart</h4>
                <p class="text-sm text-gray-800 flex items-center gap-2">
                    <i class="far fa-credit-card"></i> <?= ucfirst($order['payment_method']) ?>
                </p>
            </div>

        </div>
    </div>
</div>

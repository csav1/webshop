<?php
/**
 * Checkout Page
 * @var array $items
 * @var float $total
 * @var array $user
 */

use Core\Session;
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">
        <i class="fas fa-credit-card text-nba-blue mr-2"></i> Kasse
    </h1>

    <form action="<?= url('/kasse') ?>" method="post">
        <?= \Core\View::csrf() ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Shipping & Payment -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h2 class="font-bold text-xl mb-4">
                        <i class="fas fa-truck text-gray-400 mr-2"></i> Lieferadresse
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2">Name *</label>
                            <input type="text" name="shipping_name" required value="<?= e($user['name'] ?? '') ?>"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2">Straße & Hausnummer *</label>
                            <input type="text" name="shipping_street" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none"
                                placeholder="Musterstraße 123">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">PLZ *</label>
                            <input type="text" name="shipping_zip" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none"
                                placeholder="12345">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Stadt *</label>
                            <input type="text" name="shipping_city" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none"
                                placeholder="Musterstadt">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2">Land</label>
                            <select name="shipping_country"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none">
                                <option value="Deutschland">Deutschland</option>
                                <option value="Österreich">Österreich</option>
                                <option value="Schweiz">Schweiz</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h2 class="font-bold text-xl mb-4">
                        <i class="fas fa-wallet text-gray-400 mr-2"></i> Zahlungsart
                    </h2>
                    <div class="space-y-3">
                        <label
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-nba-red transition-colors">
                            <input type="radio" name="payment_method" value="kreditkarte" required class="text-nba-red">
                            <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                            <span class="font-medium">Kreditkarte</span>
                        </label>
                        <label
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-nba-red transition-colors">
                            <input type="radio" name="payment_method" value="paypal" class="text-nba-red">
                            <i class="fab fa-paypal text-2xl text-blue-800"></i>
                            <span class="font-medium">PayPal</span>
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">
                        <i class="fas fa-info-circle mr-1"></i> Dies ist eine Simulation - es findet keine echte Zahlung
                        statt.
                    </p>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl p-6 shadow-sm sticky top-24">
                    <h2 class="font-bold text-xl mb-4">Bestellübersicht</h2>

                    <div class="space-y-3 mb-4 max-h-60 overflow-auto">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="flex gap-3">
                                <img src="<?= !empty($item['image']) ? asset($item['image']) : asset('images/placeholder.jpg') ?>"
                                    alt="" class="w-12 h-12 rounded object-cover">
                                <div class="flex-grow min-w-0">
                                    <p class="font-medium text-sm truncate">
                                        <?= e($item['name']) ?>
                                    </p>
                                    <p class="text-gray-500 text-sm">
                                        <?= $item['quantity'] ?>x
                                        <?= formatPrice($item['price']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Zwischensumme</span>
                            <span>
                                <?= formatPrice($total) ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Versand</span>
                            <span>
                                <?= $total >= 50 ? 'Kostenlos' : formatPrice(4.99) ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between font-bold text-xl py-4 border-t border-gray-200 mt-4">
                        <span>Gesamt</span>
                        <span class="text-nba-red">
                            <?= formatPrice($total >= 50 ? $total : $total + 4.99) ?>
                        </span>
                    </div>

                    <button type="submit"
                        class="w-full bg-nba-red hover:bg-red-700 text-white py-4 rounded-lg font-bold transition-colors">
                        <i class="fas fa-check mr-2"></i> Jetzt bestellen
                    </button>

                    <p class="text-xs text-gray-500 text-center mt-4">
                        Mit Ihrer Bestellung akzeptieren Sie unsere AGB und Datenschutzbestimmungen.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
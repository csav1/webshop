<?php
/**
 * Credit Card Payment Page (Simulated)
 * @var array $order
 */
?>

<div class="min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fab fa-cc-visa text-4xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Kreditkartenzahlung</h1>
            <p class="text-gray-500 mt-2">Bestellung #<?= e($order['order_number']) ?></p>
        </div>

        <!-- Amount -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center">
            <span class="text-gray-500 text-sm">Zu zahlender Betrag</span>
            <div class="text-3xl font-bold text-nba-red"><?= formatPrice($order['total']) ?></div>
        </div>

        <!-- Form -->
        <form action="<?= url('/zahlung/kreditkarte/' . $order['order_number']) ?>" method="post" class="space-y-4">
            <?= \Core\View::csrf() ?>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Karteninhaber</label>
                <input type="text" name="card_holder" required placeholder="Max Mustermann"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Kartennummer</label>
                <input type="text" name="card_number" required placeholder="1234 5678 9012 3456" maxlength="19"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none font-mono">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Gültig bis</label>
                    <input type="text" name="expiry" required placeholder="MM/YY" maxlength="5"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">CVV</label>
                    <input type="text" name="cvv" required placeholder="123" maxlength="4"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-xl mt-6">
                <i class="fas fa-lock mr-2"></i> Jetzt bezahlen
            </button>
        </form>

        <!-- Security Note -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">
                <i class="fas fa-shield-alt mr-1"></i> Dies ist eine Simulation - keine echte Zahlung
            </p>
        </div>

        <!-- Back Link -->
        <div class="mt-4 text-center">
            <a href="<?= url('/bestellungen/' . $order['order_number']) ?>" class="text-blue-600 hover:underline text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Zurück zur Bestellung
            </a>
        </div>
    </div>
</div>

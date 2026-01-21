<?php
/**
 * PayPal Payment Page (Simulated)
 * @var array $order
 */
?>

<div class="min-h-screen bg-gradient-to-br from-blue-800 to-blue-900 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-[#003087] rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fab fa-paypal text-4xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">PayPal</h1>
            <p class="text-gray-500 mt-2">Bestellung #<?= e($order['order_number']) ?></p>
        </div>

        <!-- Amount -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center">
            <span class="text-gray-500 text-sm">Zu zahlender Betrag</span>
            <div class="text-3xl font-bold text-[#003087]"><?= formatPrice($order['total']) ?></div>
        </div>

        <!-- Form -->
        <form action="<?= url('/zahlung/paypal/' . $order['order_number']) ?>" method="post" class="space-y-4">
            <?= \Core\View::csrf() ?>

            <div>
                <label class="block text-gray-700 font-medium mb-2">PayPal E-Mail</label>
                <input type="email" name="paypal_email" required placeholder="ihre-email@beispiel.de"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#003087] focus:ring-2 focus:ring-[#003087]/20 outline-none">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">PayPal Passwort</label>
                <input type="password" name="paypal_password" required placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#003087] focus:ring-2 focus:ring-[#003087]/20 outline-none">
            </div>

            <button type="submit" 
                class="w-full bg-[#0070ba] hover:bg-[#003087] text-white py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-xl mt-6">
                <i class="fab fa-paypal mr-2"></i> Mit PayPal bezahlen
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
            <a href="<?= url('/bestellungen/' . $order['order_number']) ?>" class="text-[#003087] hover:underline text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Zurück zur Bestellung
            </a>
        </div>
    </div>
</div>

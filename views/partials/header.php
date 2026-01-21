<?php
use Core\Auth;
use Models\Cart;
use Models\Category;

$categories = Category::all();
$cartCount = Cart::count();
$user = Auth::user();
?>

<!-- Navigation -->
<nav class="bg-white/90 backdrop-blur-lg shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="<?= url('/') ?>" class="flex items-center gap-2">
                <span class="text-3xl">🏀</span>
                <span class="text-2xl font-bold gradient-text">Fake NBA Store</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-6">
                <a href="<?= url('/') ?>" class="text-gray-700 hover:text-nba-red font-medium transition-colors">
                    <i class="fas fa-home mr-1"></i> Startseite
                </a>
                <div class="relative group">
                    <button
                        class="text-gray-700 hover:text-nba-red font-medium transition-colors flex items-center gap-1">
                        <i class="fas fa-basketball mr-1"></i> Produkte
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div
                        class="absolute top-full left-0 mt-2 w-48 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="<?= url('/produkte') ?>"
                            class="block px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-nba-red rounded-t-lg">
                            Alle Produkte
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?= url('/produkte?kategorie=' . e($cat['slug'])) ?>"
                                class="block px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-nba-red">
                                <?= e($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="hidden lg:flex items-center flex-1 max-w-md mx-8">
                <form action="<?= url('/produkte') ?>" method="get" class="w-full">
                    <div class="relative">
                        <input type="text" name="suche" placeholder="Produkte suchen..."
                            class="w-full bg-gray-100 rounded-full px-5 py-2 pr-10 outline-none focus:ring-2 focus:ring-nba-red/50 transition-all">
                        <button type="submit"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-nba-red">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Cart -->
                <a href="<?= url('/warenkorb') ?>" class="relative p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <i class="fas fa-shopping-cart text-xl text-gray-700"></i>
                    <?php if ($cartCount > 0): ?>
                        <span
                            class="absolute -top-1 -right-1 bg-nba-red text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                            <?= $cartCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- User Menu -->
                <?php if (Auth::check()): ?>
                    <div class="relative group">
                        <button class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <?php if ($user && $user['avatar']): ?>
                                <img src="<?= e($user['avatar']) ?>" alt="" class="w-8 h-8 rounded-full">
                            <?php else: ?>
                                <div
                                    class="w-8 h-8 bg-nba-blue text-white rounded-full flex items-center justify-center font-bold">
                                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span class="hidden md:block text-gray-700 font-medium">
                                <?= e($user['name'] ?? 'User') ?>
                            </span>
                        </button>
                        <div
                            class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a href="<?= url('/bestellungen') ?>" class="block px-4 py-3 text-gray-700 hover:bg-orange-50 rounded-t-lg">
                                <i class="fas fa-box mr-2"></i> Meine Bestellungen
                            </a>
                            <?php if (Auth::isAdmin()): ?>
                                <a href="<?= url('/admin') ?>" class="block px-4 py-3 text-gray-700 hover:bg-orange-50">
                                    <i class="fas fa-cog mr-2"></i> Admin-Bereich
                                </a>
                            <?php endif; ?>
                            <a href="<?= url('/abmelden') ?>" class="block px-4 py-3 text-red-600 hover:bg-red-50 rounded-b-lg">
                                <i class="fas fa-sign-out-alt mr-2"></i> Abmelden
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= url('/anmelden') ?>"
                        class="hidden md:flex items-center gap-2 bg-nba-red text-white px-4 py-2 rounded-full hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-user"></i>
                        <span>Anmelden</span>
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenu" x-transition class="md:hidden bg-white border-t">
        <div class="px-4 py-4 space-y-2">
            <form action="<?= url('/produkte') ?>" method="get" class="mb-4">
                <input type="text" name="suche" placeholder="Suchen..." class="w-full bg-gray-100 rounded-lg px-4 py-2">
            </form>
            <a href="<?= url('/') ?>" class="block py-2 text-gray-700"><i class="fas fa-home mr-2"></i> Startseite</a>
            <a href="<?= url('/produkte') ?>" class="block py-2 text-gray-700"><i class="fas fa-basketball mr-2"></i> Produkte</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('/produkte?kategorie=' . e($cat['slug'])) ?>" class="block py-2 text-gray-500 pl-6">
                    <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
            <a href="<?= url('/warenkorb') ?>" class="block py-2 text-gray-700"><i class="fas fa-shopping-cart mr-2"></i>
                Warenkorb</a>
            <?php if (Auth::check()): ?>
                <a href="<?= url('/bestellungen') ?>" class="block py-2 text-gray-700"><i class="fas fa-box mr-2"></i> Bestellungen</a>
                <a href="<?= url('/abmelden') ?>" class="block py-2 text-red-600"><i class="fas fa-sign-out-alt mr-2"></i> Abmelden</a>
            <?php else: ?>
                <a href="<?= url('/anmelden') ?>" class="block py-2 text-nba-red font-medium"><i class="fas fa-user mr-2"></i>
                    Anmelden</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
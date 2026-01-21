<!DOCTYPE html>
<html lang="de" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
    require_once BASE_PATH . '/src/Helpers/seo.php';
    echo metaTags([
        'title' => $title ?? 'Admin Dashboard - NBA Shop',
        'robots' => 'noindex, nofollow'
    ]); 
    ?>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        nba: { blue: '#1d428a', red: '#c8102e', white: '#ffffff' }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside class="bg-nba-blue text-white w-64 flex-shrink-0 hidden md:flex flex-col transition-all duration-300">
        <div class="p-6 flex items-center gap-3 border-b border-blue-800">
            <i class="fas fa-basketball-ball text-2xl text-nba-red"></i>
            <span class="font-bold text-xl tracking-wider">NBA ADMIN</span>
        </div>
        
        <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
            <a href="<?= url('/admin') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors <?= is_route('/admin') ? 'bg-blue-800 text-white' : 'text-blue-100' ?>">
                <i class="fas fa-tachometer-alt w-6"></i> Dashboard
            </a>
            
            <p class="px-4 mt-8 mb-2 text-xs font-bold text-blue-300 uppercase tracking-wider">Shop Management</p>
            
            <a href="<?= url('/admin/bestellungen') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors <?= is_route_prefix('/admin/bestellungen') ? 'bg-blue-800 text-white' : 'text-blue-100' ?>">
                <i class="fas fa-shopping-bag w-6"></i> Bestellungen
            </a>
            
            <a href="<?= url('/admin/produkte') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors <?= is_route_prefix('/admin/produkte') ? 'bg-blue-800 text-white' : 'text-blue-100' ?>">
                <i class="fas fa-tshirt w-6"></i> Produkte
            </a>
            
            <a href="<?= url('/admin/kategorien') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors <?= is_route_prefix('/admin/kategorien') ? 'bg-blue-800 text-white' : 'text-blue-100' ?>">
                <i class="fas fa-tags w-6"></i> Kategorien
            </a>
            
            <a href="<?= url('/admin/benutzer') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors <?= is_route_prefix('/admin/benutzer') ? 'bg-blue-800 text-white' : 'text-blue-100' ?>">
                <i class="fas fa-users w-6"></i> Benutzer
            </a>
        </nav>
        
        <div class="p-4 border-t border-blue-800">
            <a href="<?= url('/') ?>" class="flex items-center gap-3 px-4 py-2 hover:text-blue-200 transition-colors">
                <i class="fas fa-external-link-alt"></i> Zum Shop
            </a>
            <a href="<?= url('/abmelden') ?>" class="flex items-center gap-3 px-4 py-2 text-red-300 hover:text-red-100 transition-colors">
                <i class="fas fa-sign-out-alt"></i> Abmelden
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-grow flex flex-col overflow-hidden">
        <!-- Topbar Mobile -->
        <header class="bg-white shadow-sm z-10 p-4 flex justify-between items-center md:hidden">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <span class="font-bold text-nba-blue">NBA ADMIN</span>
            </div>
            <a href="<?= url('/abmelden') ?>" class="text-gray-600">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </header>
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 flex md:hidden" style="display: none;">
            <div @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <aside class="relative bg-nba-blue text-white w-64 flex flex-col h-full shadow-xl transform transition-transform duration-300"
                   x-transition:enter="transition ease-out duration-300"
                   x-transition:enter-start="-translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-300"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="-translate-x-full">
                   
                   <!-- Mobile Menu Content duplicated -->
                   <div class="p-6 flex items-center justify-between border-b border-blue-800">
                        <span class="font-bold text-xl tracking-wider">MENU</span>
                        <button @click="sidebarOpen = false"><i class="fas fa-times"></i></button>
                   </div>
                   <!-- ... (same nav links as above) ... -->
                    <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
                        <a href="<?= url('/admin') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors">Dashboard</a>
                        <a href="<?= url('/admin/bestellungen') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors">Bestellungen</a>
                        <a href="<?= url('/admin/produkte') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors">Produkte</a>
                        <a href="<?= url('/admin/benutzer') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors">Benutzer</a>
                        <a href="<?= url('/') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors mt-8">Zum Shop</a>
                        <a href="<?= url('/abmelden') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-300 hover:bg-blue-800 transition-colors">Abmelden</a>
                    </nav>
            </aside>
        </div>

        <!-- Scrollable Content -->
        <main class="flex-grow overflow-y-auto p-4 md:p-8 bg-gray-50">
             <!-- Flash Messages -->
            <?php use Core\Session; ?>
            <?php if ($success = Session::getFlash('success')): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between shadow-sm">
                    <span><i class="fas fa-check-circle mr-2"></i> <?= \Core\View::e($success) ?></span>
                    <button onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endif; ?>

            <?php if ($error = Session::getFlash('error')): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between shadow-sm">
                    <span><i class="fas fa-exclamation-circle mr-2"></i> <?= \Core\View::e($error) ?></span>
                    <button onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endif; ?>
            
            <?= $content ?>
        </main>
    </div>

</body>
</html>

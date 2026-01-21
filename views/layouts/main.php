<!DOCTYPE html>
<html lang="de" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    // SEO Meta Tags
    require_once BASE_PATH . '/src/Helpers/seo.php';
    echo metaTags([
        'title' => $title ?? 'Fake NBA Store',
        'description' => $description ?? 'Offizielles NBA Merchandise - Trikots, Caps, Basketbälle und Sneakers.',
        'image' => $image ?? asset('images/og-image.jpg')
    ]);
    ?>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#fef3e2', 100: '#fde4bc', 200: '#fbc97a', 300: '#f9a83a', 400: '#f59015', 500: '#e67308', 600: '#c95506', 700: '#a23c09', 800: '#85300f', 900: '#6e2910' },
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

    <!-- Custom Styles -->
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gradient-text {
            background: linear-gradient(135deg, #c8102e, #1d428a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .nba-gradient {
            background: linear-gradient(135deg, #1d428a, #c8102e);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-gray-50 to-orange-50 min-h-screen flex flex-col"
    x-data="{ mobileMenu: false, cartOpen: false }">

    <?php
    use Core\Session;
    use Core\Auth;
    use Models\Cart;

    Core\View::partial('header');
    ?>

    <!-- Flash Messages -->
    <?php if ($success = Session::getFlash('success')): ?>
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between"
                role="alert">
                <span><i class="fas fa-check-circle mr-2"></i>
                    <?= e($success) ?>
                </span>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error = Session::getFlash('error')): ?>
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between"
                role="alert">
                <span><i class="fas fa-exclamation-circle mr-2"></i>
                    <?= e($error) ?>
                </span>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <?php Core\View::partial('footer'); ?>

    <!-- Scripts -->
    <script src="<?= asset('js/app.js') ?>"></script>
</body>

</html>
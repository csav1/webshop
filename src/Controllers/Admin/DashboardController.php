<?php

namespace Controllers\Admin;

use Core\View;
use Core\Auth;
use Models\Order;
use Models\Product;
use Models\User;

/**
 * Admin DashboardController
 */
class DashboardController
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /**
     * Admin Dashboard anzeigen
     */
    public function index(): void
    {
        // Statistiken abrufen
        $orderStats = Order::getStats();

        // Weitere Stats
        $totalProducts = Product::count('is_active = 1');
        $totalUsers = User::count();
        $lowStockProducts = Product::lowStock(10);

        // Neueste Bestellungen
        $recentOrders = Order::paginate(1, 10, '1=1', [], 'created_at', 'DESC');

        View::render('admin/dashboard', [
            'seo' => ['title' => 'Admin Dashboard'],
            'stats' => [
                'total_revenue' => $orderStats['total_revenue'],
                'orders_today' => $orderStats['orders_today'],
                'orders_this_week' => $orderStats['orders_this_week'],
                'pending_orders' => $orderStats['pending_orders'],
                'total_products' => $totalProducts,
                'total_users' => $totalUsers,
                'total_orders' => $orderStats['total_orders'] ?? 0,
                'recent_orders' => $recentOrders['items'],
                'low_stock' => $lowStockProducts
            ]
        ], 'layouts/admin');
    }
}

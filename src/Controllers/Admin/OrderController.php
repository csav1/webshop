<?php

namespace Controllers\Admin;

use Core\View;
use Core\Session;
use Core\Auth;
use Models\Order;
use Models\OrderItem;

/**
 * Admin OrderController - Bestellverwaltung
 */
class OrderController
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /**
     * Alle Bestellungen anzeigen
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? null;

        if ($status) {
            $orders = Order::paginate($page, 20, 'status = ?', [$status], 'created_at', 'DESC');
        } else {
            $orders = Order::paginate($page, 20, '1=1', [], 'created_at', 'DESC');
        }

        View::render('admin/orders/index', [
            'seo' => ['title' => 'Bestellungen verwalten'],
            'orders' => $orders['items'],
            'pagination' => $orders,
            'currentStatus' => $status
        ], 'layouts/admin');
    }

    /**
     * Bestelldetails anzeigen
     */
    public function show(int $id): void
    {
        $order = Order::find($id);

        if (!$order) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/admin/bestellungen');
            return;
        }

        $order['items'] = OrderItem::findByOrder($id);

        View::render('admin/orders/show', [
            'seo' => ['title' => 'Bestellung #' . $order['order_number']],
            'order' => $order
        ], 'layouts/admin');
    }

    /**
     * Bestellstatus aktualisieren
     */
    public function updateStatus(int $id): void
    {
        $order = Order::find($id);

        if (!$order) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/admin/bestellungen');
            return;
        }

        $status = $_POST['status'] ?? '';
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            Session::error('Ungültiger Status.');
            redirect('/admin/bestellungen/' . $id);
            return;
        }

        Order::updateStatus($id, $status);
        Session::success('Bestellstatus wurde aktualisiert.');
        redirect('/admin/bestellungen/' . $id);
    }

    /**
     * Zahlungsstatus aktualisieren
     */
    public function updatePaymentStatus(int $id): void
    {
        $order = Order::find($id);

        if (!$order) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/admin/bestellungen');
            return;
        }

        $status = $_POST['payment_status'] ?? '';
        $validStatuses = ['pending', 'paid', 'failed', 'refunded'];

        if (!in_array($status, $validStatuses)) {
            Session::error('Ungültiger Zahlungsstatus.');
            redirect('/admin/bestellungen/' . $id);
            return;
        }

        Order::updatePaymentStatus($id, $status);
        Session::success('Zahlungsstatus wurde aktualisiert.');
        redirect('/admin/bestellungen/' . $id);
    }
}

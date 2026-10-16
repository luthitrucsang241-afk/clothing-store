<?php
class AdminDashboardController extends AdminController
{
    public function index(): void
    {
        $this->admin('admin/dashboard', [
            'title' => 'Dashboard', 's' => Report::summary(), 'latest' => Report::latestOrders(8),
            'top' => Report::topProducts(5), 'low' => Report::lowStock(6), 'days' => Report::byDay(14),
        ]);
    }
}

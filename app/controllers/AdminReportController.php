<?php
class AdminReportController extends AdminController
{
    public function index(): void
    {
        $this->admin('admin/reports', [
            'title' => 'Báo cáo & thống kê', 's' => Report::summary(), 'days' => Report::byDay(30), 'weeks' => Report::byWeek(12), 'months' => Report::byMonth(12), 'years' => Report::byYear(), 'worst' => Report::worstProducts(10),
            'top' => Report::topProducts(10), 'customers' => Report::topCustomers(10), 'low' => Report::lowStock(20),
            'vouchers' => Report::voucherUsage(),
        ]);
    }
}

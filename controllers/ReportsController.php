<?php

/**
 * Renders the admin reports dashboard.
 */
class ReportsController
{
    private ReportsService $reportsService;

    public function __construct()
    {
        $this->reportsService = new ReportsService();
    }

    public function index(): void
    {
        $from = trim($_GET['from'] ?? '');
        $to   = trim($_GET['to']   ?? '');

        $from = $from !== '' ? $from : null;
        $to   = $to   !== '' ? $to   : null;

        $report = $this->reportsService->buildDashboard($from, $to);

        $pageTitle   = 'Reports';
        $currentPage = 'reports';
        view('reports/index', compact('report', 'from', 'to', 'pageTitle', 'currentPage'));
    }
}

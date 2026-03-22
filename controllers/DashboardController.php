<?php

/**
 * Dashboard controller - home page after login.
 */
class DashboardController
{
    public function index(): void
    {
        $pageTitle = 'Dashboard';
        view('dashboard/index', compact('pageTitle'));
    }
}

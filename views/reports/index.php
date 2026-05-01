<?php
/** @var array  $report  Dashboard data built by ReportsService::buildDashboard() */
/** @var string|null $from  Start date filter (Y-m-d) or null */
/** @var string|null $to    End date filter (Y-m-d) or null */

$kpi = $report['kpi'];

function rLabel(string $key, array $row): string {
    return Order::displayStatus($row['status'] ?? $key);
}
?>
<?php $currentPage = 'reports'; ?>

<div class="page-header">
    <h2 class="page-title">Reports</h2>
</div>

<!-- Date Filter -->
<form action="<?= baseUrl('reports') ?>" method="GET" class="reports-filter-bar">
    <div class="reports-filter-fields">
        <label class="reports-filter-label">From</label>
        <input type="date" name="from" class="form-input reports-filter-input" value="<?= e($from ?? '') ?>">
        <label class="reports-filter-label">To</label>
        <input type="date" name="to"   class="form-input reports-filter-input" value="<?= e($to ?? '') ?>">
        <button type="submit" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            Apply
        </button>
        <?php if ($from || $to): ?>
        <a href="<?= baseUrl('reports') ?>" class="btn btn-ghost btn-sm">Clear</a>
        <?php endif; ?>
    </div>
    <?php if ($from || $to): ?>
    <div class="reports-filter-active">
        Showing data from <strong><?= e($from ?? '—') ?></strong> to <strong><?= e($to ?? '—') ?></strong>
    </div>
    <?php endif; ?>
</form>

<!-- ========== KPI Cards ========== -->
<div class="reports-kpi-grid">
    <div class="reports-kpi-card">
        <div class="reports-kpi-label">Total Sold</div>
        <div class="reports-kpi-value reports-kpi-primary">$<?= e(number_format($kpi['total_sold'], 2)) ?></div>
    </div>
    <div class="reports-kpi-card">
        <div class="reports-kpi-label">Total Orders</div>
        <div class="reports-kpi-value"><?= (int) $kpi['total_orders'] ?></div>
    </div>
    <div class="reports-kpi-card">
        <div class="reports-kpi-label">Active Customers</div>
        <div class="reports-kpi-value reports-kpi-success"><?= (int) $kpi['active_customers'] ?></div>
    </div>
    <div class="reports-kpi-card">
        <div class="reports-kpi-label">Suspended Customers</div>
        <div class="reports-kpi-value reports-kpi-danger"><?= (int) $kpi['suspended_customers'] ?></div>
    </div>
    <div class="reports-kpi-card">
        <div class="reports-kpi-label">Active Drivers</div>
        <div class="reports-kpi-value"><?= (int) $kpi['active_drivers'] ?></div>
    </div>
    <div class="reports-kpi-card">
        <div class="reports-kpi-label">Active Restaurants</div>
        <div class="reports-kpi-value"><?= (int) $kpi['active_restaurants'] ?></div>
    </div>
    <div class="reports-kpi-card">
        <div class="reports-kpi-label">Peak Order Hour</div>
        <div class="reports-kpi-value">
            <?php if ($kpi['peak_hour']): ?>
                <?= sprintf('%02d:00 – %02d:00', (int)$kpi['peak_hour']['hour_bucket'], (int)$kpi['peak_hour']['hour_bucket'] + 1) ?>
                <span class="reports-kpi-sub"><?= (int) $kpi['peak_hour']['total'] ?> orders</span>
            <?php else: ?>
                <span style="color:var(--color-text-secondary); font-size:var(--font-size-sm);">No data</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========== Restaurant Performance ========== -->
<div class="reports-section card">
    <div class="reports-section-header">
        <h3 class="reports-section-title">Restaurant Performance</h3>
    </div>

    <?php if ($report['top_restaurant'] || $report['bottom_restaurant']): ?>
    <div class="reports-highlight-grid">
        <?php if ($report['top_restaurant']): ?>
        <div class="reports-highlight reports-highlight-top">
            <div class="reports-highlight-badge">Most Orders</div>
            <div class="reports-highlight-name"><?= e($report['top_restaurant']['name']) ?></div>
            <div class="reports-highlight-stat"><?= (int) $report['top_restaurant']['total_orders'] ?> orders &nbsp;·&nbsp; $<?= e(number_format((float) $report['top_restaurant']['total_revenue'], 2)) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($report['bottom_restaurant']): ?>
        <div class="reports-highlight reports-highlight-bottom">
            <div class="reports-highlight-badge">Least Orders</div>
            <div class="reports-highlight-name"><?= e($report['bottom_restaurant']['name']) ?></div>
            <div class="reports-highlight-stat"><?= (int) $report['bottom_restaurant']['total_orders'] ?> orders &nbsp;·&nbsp; $<?= e(number_format((float) $report['bottom_restaurant']['total_revenue'], 2)) ?></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($report['restaurants'])): ?>
    <div class="empty-state"><p class="empty-state-text">No restaurant data available.</p></div>
    <?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Restaurant</th>
                    <th>Food Type</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Total Orders</th>
                    <th style="text-align:right;">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['restaurants'] as $r): ?>
                <tr>
                    <td><strong><?= e($r['name']) ?></strong></td>
                    <td><span class="food-type-chip"><?= e($r['food_type']) ?></span></td>
                    <td style="text-align:center;">
                        <?php if ((int) $r['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;"><?= (int) $r['total_orders'] ?></td>
                    <td style="text-align:right; font-weight:600;">$<?= e(number_format((float) $r['total_revenue'], 2)) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- ========== Orders Breakdown ========== -->
<div class="reports-section card">
    <div class="reports-section-header">
        <h3 class="reports-section-title">Orders Breakdown</h3>
    </div>

    <div class="reports-two-col">
        <!-- By Status -->
        <div>
            <div class="reports-subsection-title">By Status</div>
            <?php if (empty($report['orders_by_status'])): ?>
            <p class="empty-state-text">No orders.</p>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Status</th><th style="text-align:right;">Count</th></tr></thead>
                <tbody>
                    <?php foreach ($report['orders_by_status'] as $row): ?>
                    <tr>
                        <td><span class="order-status order-status-<?= e($row['status']) ?>"><?= e(Order::displayStatus($row['status'])) ?></span></td>
                        <td style="text-align:right;"><?= (int) $row['total'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Peak Hours -->
        <div>
            <div class="reports-subsection-title">Peak Order Hours</div>
            <?php if (empty($report['peak_hours'])): ?>
            <p class="empty-state-text">No data.</p>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Hour</th><th style="text-align:right;">Orders</th></tr></thead>
                <tbody>
                    <?php foreach (array_slice($report['peak_hours'], 0, 10) as $row): ?>
                    <tr>
                        <td><?= sprintf('%02d:00 – %02d:00', (int)$row['hour_bucket'], (int)$row['hour_bucket'] + 1) ?></td>
                        <td style="text-align:right;"><?= (int) $row['total'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========== Orders by Customer ========== -->
<div class="reports-section card">
    <div class="reports-section-header">
        <h3 class="reports-section-title">Orders by Customer</h3>
    </div>
    <?php if (empty($report['orders_by_customer'])): ?>
    <div class="empty-state"><p class="empty-state-text">No orders found.</p></div>
    <?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th style="text-align:center;">Total Orders</th>
                    <th style="text-align:right;">Total Spent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['orders_by_customer'] as $row): ?>
                <tr>
                    <td><strong><?= e($row['customer_name']) ?></strong></td>
                    <td style="color:var(--color-text-secondary);"><?= e($row['email']) ?></td>
                    <td style="text-align:center;"><?= (int) $row['total_orders'] ?></td>
                    <td style="text-align:right; font-weight:600;">$<?= e(number_format((float) $row['total_spent'], 2)) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- ========== Delivery Drivers ========== -->
<div class="reports-section card">
    <div class="reports-section-header">
        <h3 class="reports-section-title">Delivery Drivers</h3>
    </div>
    <?php if (empty($report['drivers'])): ?>
    <div class="empty-state"><p class="empty-state-text">No drivers found.</p></div>
    <?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Phone</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Warnings</th>
                    <th style="text-align:center;">Deliveries</th>
                    <th style="text-align:center;">Complaints</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['drivers'] as $d): ?>
                <tr>
                    <td><strong><?= e($d['full_name']) ?></strong></td>
                    <td style="color:var(--color-text-secondary);"><?= e($d['phone']) ?></td>
                    <td style="text-align:center;">
                        <?php if ($d['status'] === 'available'): ?>
                            <span class="badge badge-success">Available</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Busy</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if ((int) $d['warning_count'] > 0): ?>
                            <span class="badge badge-danger"><?= (int) $d['warning_count'] ?></span>
                        <?php else: ?>
                            <span style="color:var(--color-text-secondary);">0</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;"><?= (int) $d['total_deliveries'] ?></td>
                    <td style="text-align:center;">
                        <?php if ((int) $d['complaint_count'] > 0): ?>
                            <span class="badge badge-danger"><?= (int) $d['complaint_count'] ?></span>
                        <?php else: ?>
                            <span style="color:var(--color-text-secondary);">0</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- ========== Customers ========== -->
<div class="reports-section card">
    <div class="reports-section-header">
        <h3 class="reports-section-title">Customers</h3>
    </div>

    <!-- Active -->
    <div class="reports-subsection-title" style="margin-bottom:var(--space-sm);">
        Active Customers
        <span class="badge badge-success" style="margin-left:6px;"><?= count($report['active_customers']) ?></span>
    </div>
    <?php if (empty($report['active_customers'])): ?>
    <p class="empty-state-text" style="margin-bottom:var(--space-lg);">No active customers.</p>
    <?php else: ?>
    <div class="table-container" style="margin-bottom:var(--space-xl);">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Member Since</th></tr></thead>
            <tbody>
                <?php foreach ($report['active_customers'] as $c): ?>
                <tr>
                    <td><strong><?= e($c['first_name'] . ' ' . $c['last_name']) ?></strong></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['phone_number'] ?? '—') ?></td>
                    <td><?= e($c['city'] ?? '—') ?></td>
                    <td style="color:var(--color-text-secondary); font-size:var(--font-size-xs);"><?= e(date('M j, Y', strtotime($c['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Suspended -->
    <div class="reports-subsection-title" style="margin-bottom:var(--space-sm);">
        Suspended Customers
        <span class="badge badge-danger" style="margin-left:6px;"><?= count($report['suspended_customers']) ?></span>
    </div>
    <?php if (empty($report['suspended_customers'])): ?>
    <p class="empty-state-text">No suspended customers.</p>
    <?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>City</th></tr></thead>
            <tbody>
                <?php foreach ($report['suspended_customers'] as $c): ?>
                <tr>
                    <td><strong><?= e($c['first_name'] . ' ' . $c['last_name']) ?></strong></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['phone_number'] ?? '—') ?></td>
                    <td><?= e($c['city'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

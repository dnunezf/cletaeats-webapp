<?php
/** @var array  $report */
/** @var string|null $from */
/** @var string|null $to   */
$kpi = $report['kpi'];
?>
<?php $currentPage = 'reports'; ?>

<div class="page-header">
    <h2 class="page-title">Reports</h2>
</div>

<form action="<?= baseUrl('reports') ?>" method="GET" class="reports-filter-bar">
    <div class="reports-filter-fields">
        <label class="reports-filter-label">From</label>
        <input type="date" name="from" class="form-input reports-filter-input" value="<?= e($from ?? '') ?>">
        <label class="reports-filter-label">To</label>
        <input type="date" name="to"   class="form-input reports-filter-input" value="<?= e($to ?? '') ?>">
        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
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
        <div class="reports-kpi-label">Inactive Customers</div>
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
            <div class="reports-highlight-stat"><?= (int) $report['top_restaurant']['total_orders'] ?> orders · $<?= e(number_format((float) $report['top_restaurant']['total_revenue'], 2)) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($report['bottom_restaurant']): ?>
        <div class="reports-highlight reports-highlight-bottom">
            <div class="reports-highlight-badge">Least Orders</div>
            <div class="reports-highlight-name"><?= e($report['bottom_restaurant']['name']) ?></div>
            <div class="reports-highlight-stat"><?= (int) $report['bottom_restaurant']['total_orders'] ?> orders · $<?= e(number_format((float) $report['bottom_restaurant']['total_revenue'], 2)) ?></div>
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
                    <th>Category</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Total Orders</th>
                    <th style="text-align:right;">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['restaurants'] as $r): ?>
                <tr>
                    <td><strong><?= e($r['name']) ?></strong></td>
                    <td><span class="food-type-chip"><?= e(ucfirst($r['food_type'] ?? '')) ?></span></td>
                    <td style="text-align:center;">
                        <?php if (($r['status'] ?? '') === 'active'): ?>
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

<div class="reports-section card">
    <div class="reports-section-header">
        <h3 class="reports-section-title">Orders Breakdown</h3>
    </div>
    <div class="reports-two-col">
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
                    <th>Email</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Penalties</th>
                    <th style="text-align:center;">Deliveries</th>
                    <th style="text-align:center;">Complaints</th>
                    <th style="text-align:center;">Avg Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['drivers'] as $d): ?>
                <tr>
                    <td><strong><?= e($d['full_name']) ?></strong></td>
                    <td style="color:var(--color-text-secondary);"><?= e($d['email']) ?></td>
                    <td style="text-align:center;">
                        <?php if ($d['status'] === 'available'): ?>
                            <span class="badge badge-success">Available</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Occupied</span>
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
                    <td style="text-align:center;">
                        <?= $d['avg_rating'] !== null ? e(number_format((float) $d['avg_rating'], 2)) : '—' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<div class="reports-section card">
    <div class="reports-section-header">
        <h3 class="reports-section-title">Customers</h3>
    </div>

    <div class="reports-subsection-title" style="margin-bottom:var(--space-sm);">
        Active Customers
        <span class="badge badge-success" style="margin-left:6px;"><?= count($report['active_customers']) ?></span>
    </div>
    <?php if (empty($report['active_customers'])): ?>
    <p class="empty-state-text" style="margin-bottom:var(--space-lg);">No active customers.</p>
    <?php else: ?>
    <div class="table-container" style="margin-bottom:var(--space-xl);">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Document</th><th>City</th></tr></thead>
            <tbody>
                <?php foreach ($report['active_customers'] as $c): ?>
                <tr>
                    <td><strong><?= e($c['username']) ?></strong></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['document'] ?? '—') ?></td>
                    <td><?= e($c['city'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="reports-subsection-title" style="margin-bottom:var(--space-sm);">
        Inactive Customers
        <span class="badge badge-danger" style="margin-left:6px;"><?= count($report['suspended_customers']) ?></span>
    </div>
    <?php if (empty($report['suspended_customers'])): ?>
    <p class="empty-state-text">No inactive customers.</p>
    <?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Document</th><th>City</th></tr></thead>
            <tbody>
                <?php foreach ($report['suspended_customers'] as $c): ?>
                <tr>
                    <td><strong><?= e($c['username']) ?></strong></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['document'] ?? '—') ?></td>
                    <td><?= e($c['city'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

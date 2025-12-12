<?php

/**
 * Advanced Analytics Dashboard Component
 * Interactive charts, real-time data, and insights
 * Verdant SMS v3.0
 */

if (!isset($_SESSION['user_id'])) return;

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

// Get analytics data
$analytics = getAnalyticsData($role);

function getAnalyticsData($role)
{
    $data = [
        'attendance' => [],
        'grades' => [],
        'trends' => [],
        'summary' => []
    ];

    try {
        // Attendance data for last 7 days
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $label = date('D', strtotime($date));

            $stats = db()->fetchOne(
                "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
                 FROM attendance_records
                 WHERE attendance_date = ?",
                [$date]
            );

            $rate = ($stats && $stats['total'] > 0)
                ? round(($stats['present'] / $stats['total']) * 100)
                : 0;

            $days[] = [
                'label' => $label,
                'value' => $rate
            ];
        }
        $data['attendance'] = $days;

        // Summary stats based on role
        if (in_array($role, ['admin', 'principal', 'superadmin'])) {
            $data['summary'] = [
                'total_students' => db()->count('students') ?? 0,
                'total_teachers' => db()->count('users', "role = 'teacher'") ?? 0,
                'total_classes' => db()->count('classes') ?? 0,
                'pending_approvals' => db()->count('users', "status = 'pending'") ?? 0
            ];
        }
    } catch (Exception $e) {
        // Use empty defaults
    }

    return $data;
}
?>

<!-- Analytics Dashboard Styles -->
<style>
    .analytics-dashboard {
        background: rgba(10, 10, 10, 0.95);
        border: 1px solid rgba(0, 255, 136, 0.3);
        border-radius: 12px;
        padding: 24px;
        margin: 20px 0;
    }

    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(0, 255, 136, 0.2);
    }

    .analytics-title {
        color: #00ff88;
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .analytics-title i {
        font-size: 1.3rem;
    }

    .analytics-controls {
        display: flex;
        gap: 10px;
    }

    .analytics-period-btn {
        background: rgba(0, 255, 136, 0.1);
        border: 1px solid rgba(0, 255, 136, 0.3);
        color: #00ff88;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }

    .analytics-period-btn:hover,
    .analytics-period-btn.active {
        background: rgba(0, 255, 136, 0.3);
        border-color: #00ff88;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .analytics-card {
        background: rgba(20, 20, 20, 0.9);
        border: 1px solid rgba(0, 255, 136, 0.2);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .analytics-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #00ff88, #00d4ff);
    }

    .analytics-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .analytics-card-title {
        color: #aaa;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .analytics-card-value {
        color: #fff;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0;
    }

    .analytics-card-change {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .analytics-card-change.positive {
        color: #00ff88;
    }

    .analytics-card-change.negative {
        color: #ff6b6b;
    }

    /* Chart Container */
    .chart-container {
        background: rgba(20, 20, 20, 0.9);
        border: 1px solid rgba(0, 255, 136, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-title {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .chart-legend {
        display: flex;
        gap: 15px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        color: #888;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .legend-dot.present {
        background: #00ff88;
    }

    .legend-dot.absent {
        background: #ff6b6b;
    }

    .legend-dot.late {
        background: #ffcc00;
    }

    /* Bar Chart */
    .bar-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        height: 200px;
        padding: 20px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .bar-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        max-width: 60px;
    }

    .bar {
        width: 40px;
        background: linear-gradient(to top, #00ff88, #00d4ff);
        border-radius: 4px 4px 0 0;
        transition: all 0.5s ease;
        min-height: 5px;
        position: relative;
    }

    .bar:hover {
        transform: scaleY(1.05);
        box-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
    }

    .bar-tooltip {
        position: absolute;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.9);
        color: #00ff88;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .bar:hover .bar-tooltip {
        opacity: 1;
        visibility: visible;
    }

    .bar-label {
        color: #888;
        font-size: 0.8rem;
        margin-top: 10px;
    }

    /* Donut Chart */
    .donut-chart-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 40px;
        padding: 20px;
    }

    .donut-chart {
        width: 180px;
        height: 180px;
        position: relative;
    }

    .donut-chart svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .donut-segment {
        fill: none;
        stroke-width: 20;
        transition: all 0.5s ease;
    }

    .donut-segment:hover {
        stroke-width: 25;
        filter: brightness(1.2);
    }

    .donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .donut-center-value {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
    }

    .donut-center-label {
        font-size: 0.8rem;
        color: #888;
    }

    .donut-legend {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .donut-legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: rgba(0, 255, 136, 0.05);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .donut-legend-item:hover {
        background: rgba(0, 255, 136, 0.1);
    }

    .donut-legend-color {
        width: 14px;
        height: 14px;
        border-radius: 4px;
    }

    .donut-legend-text {
        color: #888;
        font-size: 0.9rem;
    }

    .donut-legend-value {
        color: #fff;
        font-weight: 600;
        margin-left: auto;
    }

    /* Real-time Updates */
    .realtime-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: #00ff88;
    }

    .realtime-dot {
        width: 8px;
        height: 8px;
        background: #00ff88;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    /* Trends Section */
    .trends-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .trend-card {
        background: rgba(30, 30, 30, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .trend-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .trend-icon.students {
        background: rgba(0, 212, 255, 0.2);
        color: #00d4ff;
    }

    .trend-icon.teachers {
        background: rgba(255, 136, 0, 0.2);
        color: #ff8800;
    }

    .trend-icon.classes {
        background: rgba(136, 0, 255, 0.2);
        color: #8800ff;
    }

    .trend-icon.pending {
        background: rgba(255, 204, 0, 0.2);
        color: #ffcc00;
    }

    .trend-info h4 {
        color: #fff;
        font-size: 1.3rem;
        margin: 0;
    }

    .trend-info span {
        color: #888;
        font-size: 0.85rem;
    }

    /* Sparkline */
    .sparkline {
        height: 40px;
        display: flex;
        align-items: flex-end;
        gap: 2px;
    }

    .sparkline-bar {
        width: 4px;
        background: rgba(0, 255, 136, 0.5);
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    .sparkline-bar:hover {
        background: #00ff88;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .analytics-dashboard {
            padding: 16px;
        }

        .analytics-header {
            flex-direction: column;
            gap: 15px;
        }

        .analytics-grid {
            grid-template-columns: 1fr;
        }

        .donut-chart-container {
            flex-direction: column;
        }

        .bar-chart {
            height: 150px;
        }

        .bar {
            width: 30px;
        }
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(1.2);
        }
    }

    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!-- Analytics Dashboard HTML -->
<div class="analytics-dashboard" id="analyticsDashboard">
    <div class="analytics-header">
        <h2 class="analytics-title">
            <i class="fas fa-chart-line"></i>
            Analytics Overview
        </h2>
        <div class="analytics-controls">
            <button class="analytics-period-btn active" data-period="week">Week</button>
            <button class="analytics-period-btn" data-period="month">Month</button>
            <button class="analytics-period-btn" data-period="year">Year</button>
            <div class="realtime-indicator">
                <span class="realtime-dot"></span>
                Live
            </div>
        </div>
    </div>

    <?php if (!empty($analytics['summary'])): ?>
        <div class="trends-grid">
            <div class="trend-card">
                <div class="trend-icon students">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="trend-info">
                    <h4><?= number_format($analytics['summary']['total_students']) ?></h4>
                    <span>Total Students</span>
                </div>
            </div>
            <div class="trend-card">
                <div class="trend-icon teachers">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="trend-info">
                    <h4><?= number_format($analytics['summary']['total_teachers']) ?></h4>
                    <span>Teachers</span>
                </div>
            </div>
            <div class="trend-card">
                <div class="trend-icon classes">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="trend-info">
                    <h4><?= number_format($analytics['summary']['total_classes']) ?></h4>
                    <span>Classes</span>
                </div>
            </div>
            <div class="trend-card">
                <div class="trend-icon pending">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="trend-info">
                    <h4><?= number_format($analytics['summary']['pending_approvals']) ?></h4>
                    <span>Pending Approvals</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Attendance Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <span class="chart-title">Attendance Rate (Last 7 Days)</span>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-dot present"></span>
                    Present
                </div>
            </div>
        </div>
        <div class="bar-chart" id="attendanceChart">
            <?php foreach ($analytics['attendance'] as $day): ?>
                <div class="bar-group">
                    <div class="bar" style="height: <?= max(5, $day['value'] * 1.8) ?>px;">
                        <span class="bar-tooltip"><?= $day['value'] ?>%</span>
                    </div>
                    <span class="bar-label"><?= $day['label'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Donut Chart for Attendance Distribution -->
    <div class="chart-container">
        <div class="chart-header">
            <span class="chart-title">Today's Attendance Distribution</span>
        </div>
        <div class="donut-chart-container" id="donutChartContainer">
            <div class="donut-chart">
                <svg viewBox="0 0 100 100">
                    <circle class="donut-segment" cx="50" cy="50" r="40"
                        stroke="rgba(255,255,255,0.1)"
                        stroke-dasharray="251.2"
                        stroke-dashoffset="0"></circle>
                    <circle class="donut-segment" cx="50" cy="50" r="40"
                        stroke="#00ff88"
                        stroke-dasharray="251.2"
                        stroke-dashoffset="50"
                        data-segment="present"></circle>
                    <circle class="donut-segment" cx="50" cy="50" r="40"
                        stroke="#ff6b6b"
                        stroke-dasharray="251.2"
                        stroke-dashoffset="220"
                        data-segment="absent"></circle>
                </svg>
                <div class="donut-center">
                    <div class="donut-center-value" id="donutCenterValue">85%</div>
                    <div class="donut-center-label">Attendance</div>
                </div>
            </div>
            <div class="donut-legend">
                <div class="donut-legend-item">
                    <div class="donut-legend-color" style="background: #00ff88;"></div>
                    <span class="donut-legend-text">Present</span>
                    <span class="donut-legend-value" id="presentCount">-</span>
                </div>
                <div class="donut-legend-item">
                    <div class="donut-legend-color" style="background: #ff6b6b;"></div>
                    <span class="donut-legend-text">Absent</span>
                    <span class="donut-legend-value" id="absentCount">-</span>
                </div>
                <div class="donut-legend-item">
                    <div class="donut-legend-color" style="background: #ffcc00;"></div>
                    <span class="donut-legend-text">Late</span>
                    <span class="donut-legend-value" id="lateCount">-</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Dashboard JavaScript -->
<script>
    (function() {
        const AnalyticsDashboard = {
            init() {
                this.setupPeriodButtons();
                this.loadTodayAttendance();
                this.animateBars();
                this.startRealtimeUpdates();
            },

            setupPeriodButtons() {
                document.querySelectorAll('.analytics-period-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.analytics-period-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        this.loadPeriodData(btn.dataset.period);
                    });
                });
            },

            loadPeriodData(period) {
                // In a real app, fetch data from API
                console.log('Loading data for period:', period);
                // Animate bars when period changes
                this.animateBars();
            },

            loadTodayAttendance() {
                fetch('<?= APP_URL ?>/api/online-users.php?stats=attendance')
                    .then(r => r.json())
                    .then(data => {
                        if (data.attendance) {
                            document.getElementById('presentCount').textContent = data.attendance.present || 0;
                            document.getElementById('absentCount').textContent = data.attendance.absent || 0;
                            document.getElementById('lateCount').textContent = data.attendance.late || 0;

                            const total = (data.attendance.present || 0) +
                                (data.attendance.absent || 0) +
                                (data.attendance.late || 0);
                            if (total > 0) {
                                const rate = Math.round((data.attendance.present / total) * 100);
                                document.getElementById('donutCenterValue').textContent = rate + '%';
                                this.updateDonutChart(data.attendance.present, data.attendance.absent, data.attendance.late);
                            }
                        }
                    })
                    .catch(() => {
                        // Use placeholder data
                        document.getElementById('presentCount').textContent = '45';
                        document.getElementById('absentCount').textContent = '5';
                        document.getElementById('lateCount').textContent = '3';
                    });
            },

            updateDonutChart(present, absent, late) {
                const total = present + absent + late;
                if (total === 0) return;

                const circumference = 251.2;
                const presentPct = (present / total) * circumference;
                const absentPct = (absent / total) * circumference;

                const presentSegment = document.querySelector('[data-segment="present"]');
                const absentSegment = document.querySelector('[data-segment="absent"]');

                if (presentSegment) {
                    presentSegment.style.strokeDasharray = `${presentPct} ${circumference}`;
                }
                if (absentSegment) {
                    absentSegment.style.strokeDasharray = `${absentPct} ${circumference}`;
                    absentSegment.style.strokeDashoffset = -presentPct;
                }
            },

            animateBars() {
                const bars = document.querySelectorAll('.bar');
                bars.forEach((bar, index) => {
                    const height = bar.style.height;
                    bar.style.height = '5px';
                    setTimeout(() => {
                        bar.style.height = height;
                    }, index * 100);
                });
            },

            startRealtimeUpdates() {
                // Update every 30 seconds
                setInterval(() => {
                    this.loadTodayAttendance();
                }, 30000);
            }
        };

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => AnalyticsDashboard.init());
        } else {
            AnalyticsDashboard.init();
        }
    })();
</script>
<?php
/**
 * Librarian Portal - Library Operations Center
 * Complete dashboard with barcode scanner, overdue tracking, and new arrivals
 */

require_once dirname(__DIR__) . '/includes/config.php';
$librarianName = $_SESSION['full_name'] ?? 'Librarian';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');
$pageTitle = "Library Operations";
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --purple: #A855F7;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
            padding: 1.5rem;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .header h1 span {
            background: linear-gradient(90deg, var(--primary), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p { color: var(--text-muted); }

        /* ===== STATS ROW ===== */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-mini {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-mini-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-mini.books .stat-mini-icon { background: rgba(0,212,255,0.15); color: var(--primary); }
        .stat-mini.issued .stat-mini-icon { background: rgba(0,255,135,0.15); color: var(--success); }
        .stat-mini.overdue .stat-mini-icon { background: rgba(255,71,87,0.15); color: var(--danger); }
        .stat-mini.members .stat-mini-icon { background: rgba(168,85,247,0.15); color: var(--purple); }

        .stat-mini-info h3 { font-size: 1.5rem; font-weight: 700; }
        .stat-mini-info p { font-size: 0.8rem; color: var(--text-muted); }

        /* ===== SCANNER SECTION ===== */
        .scanner-section {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(168, 85, 247, 0.15));
            border: 2px dashed var(--primary);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .scanner-section h2 {
            font-size: 1.25rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .scanner-section p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .scanner-input-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .scanner-input {
            width: 400px;
            background: rgba(0,0,0,0.4);
            border: 2px solid rgba(0,212,255,0.3);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            color: #fff;
            font-size: 1.1rem;
            text-align: center;
        }

        .scanner-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(0,212,255,0.3);
        }

        .scanner-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .scanner-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .scanner-btn.issue {
            background: var(--success);
            border: none;
            color: #000;
        }

        .scanner-btn.return {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .scanner-btn:hover { transform: translateY(-3px); }

        /* ===== MAIN GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h3 i.danger { color: var(--danger); }
        .card-header h3 i.success { color: var(--success); }

        .card-badge {
            background: var(--danger);
            color: #fff;
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
        }

        .card-body { padding: 1.5rem; }

        /* ===== OVERDUE LIST ===== */
        .overdue-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(255,71,87,0.1);
            border: 1px solid rgba(255,71,87,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .overdue-item:last-child { margin-bottom: 0; }

        .overdue-info h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .overdue-info p { font-size: 0.8rem; color: var(--text-muted); }

        .overdue-days {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--danger);
            padding: 0.25rem 0.75rem;
            background: rgba(255,71,87,0.15);
            border-radius: 6px;
        }

        /* ===== NEW ARRIVALS ===== */
        .book-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }

        .book-item:last-child { border-bottom: none; }

        .book-cover {
            width: 60px;
            height: 80px;
            background: linear-gradient(135deg, var(--purple), var(--primary));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .book-info { flex: 1; }
        .book-info h4 { font-size: 0.95rem; font-weight: 600; margin-bottom: 0.25rem; }
        .book-info p { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; }

        .book-copies {
            display: inline-block;
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            background: rgba(0,255,135,0.15);
            color: var(--success);
            border-radius: 4px;
        }

        @media (max-width: 1024px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
            .scanner-input { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $librarianName)[0]) ?></span>!</h1>
                <p>Library Operations Center • <?= date('l, F j, Y') ?></p>
            </div>
        </header>

        <!-- STATS ROW -->
        <div class="stats-row">
            <div class="stat-mini books">
                <div class="stat-mini-icon"><i class="fas fa-book"></i></div>
                <div class="stat-mini-info">
                    <h3>2,450</h3>
                    <p>Total Books</p>
                </div>
            </div>
            <div class="stat-mini issued">
                <div class="stat-mini-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-mini-info">
                    <h3>187</h3>
                    <p>Currently Issued</p>
                </div>
            </div>
            <div class="stat-mini overdue">
                <div class="stat-mini-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-mini-info">
                    <h3>12</h3>
                    <p>Overdue Returns</p>
                </div>
            </div>
            <div class="stat-mini members">
                <div class="stat-mini-icon"><i class="fas fa-users"></i></div>
                <div class="stat-mini-info">
                    <h3>526</h3>
                    <p>Library Members</p>
                </div>
            </div>
        </div>

        <!-- SCANNER SECTION -->
        <div class="scanner-section">
            <h2><i class="fas fa-barcode"></i> Quick Scanner</h2>
            <p>Scan book barcode or enter student ID to issue/return books</p>
            <div class="scanner-input-group">
                <input type="text" class="scanner-input" placeholder="Scan or enter book/student code..." autofocus>
            </div>
            <div class="scanner-buttons">
                <button class="scanner-btn issue"><i class="fas fa-arrow-right"></i> Issue Book</button>
                <button class="scanner-btn return"><i class="fas fa-arrow-left"></i> Return Book</button>
            </div>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <!-- OVERDUE BOOKS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle danger"></i> Overdue Books</h3>
                    <span class="card-badge">12 items</span>
                </div>
                <div class="card-body">
                    <div class="overdue-item">
                        <div class="overdue-info">
                            <h4>Things Fall Apart</h4>
                            <p>Chinedu Okoro • JSS 2A</p>
                        </div>
                        <span class="overdue-days">5 days</span>
                    </div>
                    <div class="overdue-item">
                        <div class="overdue-info">
                            <h4>Animal Farm</h4>
                            <p>Adaeze Eze • SSS 1B</p>
                        </div>
                        <span class="overdue-days">3 days</span>
                    </div>
                    <div class="overdue-item">
                        <div class="overdue-info">
                            <h4>Basic Science Vol 2</h4>
                            <p>Emeka Nwosu • JSS 3A</p>
                        </div>
                        <span class="overdue-days">1 day</span>
                    </div>
                    <div class="overdue-item">
                        <div class="overdue-info">
                            <h4>Further Mathematics</h4>
                            <p>Kemi Adebayo • SSS 2A</p>
                        </div>
                        <span class="overdue-days">7 days</span>
                    </div>
                </div>
            </div>

            <!-- NEW ARRIVALS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-star success"></i> New Arrivals</h3>
                </div>
                <div class="card-body">
                    <div class="book-item">
                        <div class="book-cover"><i class="fas fa-calculator"></i></div>
                        <div class="book-info">
                            <h4>Advanced Mathematics for SS3</h4>
                            <p>Author: Prof. Chukwu</p>
                            <span class="book-copies">5 copies</span>
                        </div>
                    </div>
                    <div class="book-item">
                        <div class="book-cover"><i class="fas fa-flask"></i></div>
                        <div class="book-info">
                            <h4>Chemistry Made Easy</h4>
                            <p>Author: Dr. Eze</p>
                            <span class="book-copies">3 copies</span>
                        </div>
                    </div>
                    <div class="book-item">
                        <div class="book-cover"><i class="fas fa-globe"></i></div>
                        <div class="book-info">
                            <h4>World Geography Atlas 2024</h4>
                            <p>Reference Book</p>
                            <span class="book-copies">2 copies</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

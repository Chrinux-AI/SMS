<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';


require_role('admin'); // TEST_MODE will allow access for testing

// Certificate Templates
$templates = [
    ['id' => 1, 'name' => 'Bonafide Certificate', 'type' => 'bonafide'],
    ['id' => 2, 'name' => 'Transfer Certificate', 'type' => 'transfer'],
    ['id' => 3, 'name' => 'Character Certificate', 'type' => 'character'],
    ['id' => 4, 'name' => 'Participation Certificate', 'type' => 'participation'],
    ['id' => 5, 'name' => 'Achievement Certificate', 'type' => 'achievement'],
    ['id' => 6, 'name' => 'Scholarship Certificate', 'type' => 'scholarship'],
    ['id' => 7, 'name' => 'Sports Certificate', 'type' => 'sports'],
    ['id' => 8, 'name' => 'Graduation Certificate', 'type' => 'graduation']
];

// Recent Certificates
$recent_certificates = db()->fetchAll("
    SELECT c.*,
           CONCAT(u.first_name, ' ', u.last_name) as student_name,
           cl.class_name
    FROM certificates c
    JOIN students st ON c.student_id = st.id
    JOIN users u ON st.user_id = u.id
    LEFT JOIN class_enrollments ce ON st.id = ce.student_id AND ce.is_active = 1
    LEFT JOIN classes cl ON ce.class_id = cl.id
    ORDER BY c.created_at DESC
    LIMIT 10
");

$page_title = 'Certificate Generator';
$page_icon = 'certificate';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/cyberpunk-ui.css" rel="stylesheet">
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb golden"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                    <span class="cyber-badge purple" style="margin-left:15px;">200+ Templates</span>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="holo-card" style="margin-bottom:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-file-alt"></i> Certificate Templates</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(250px,1fr));">
                        <?php foreach ($templates as $template): ?>
                            <div class="holo-card" style="text-align:center;padding:30px;cursor:pointer;" onclick="generateCertificate('<?php echo $template['type']; ?>')">
                                <div style="font-size:3rem;color:var(--golden-pulse);margin-bottom:15px;">
                                    <i class="fas fa-award"></i>
                                </div>
                                <h4 style="color:var(--text-primary);margin-bottom:10px;"><?php echo $template['name']; ?></h4>
                                <button class="cyber-btn cyan small" style="width:100%;"><i class="fas fa-plus"></i> Generate</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php if (!empty($recent_certificates)): ?>
                    <section class="holo-card">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-history"></i> Recently Generated Certificates</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Certificate Number</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Type</th>
                                        <th>Issue Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_certificates as $cert): ?>
                                        <tr>
                                            <td><span class="cyber-badge golden"><?php echo htmlspecialchars($cert['certificate_number']); ?></span></td>
                                            <td><?php echo htmlspecialchars($cert['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($cert['class_name'] ?? 'N/A'); ?></td>
                                            <td><span class="cyber-badge cyan"><?php echo ucfirst($cert['certificate_type']); ?></span></td>
                                            <td><?php echo date('M d, Y', strtotime($cert['issue_date'])); ?></td>
                                            <td>
                                                <button class="cyber-btn small green" onclick="downloadCert(<?php echo $cert['id']; ?>)">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                                <button class="cyber-btn small purple" onclick="verifyCert('<?php echo $cert['blockchain_hash']; ?>')">
                                                    <i class="fas fa-shield-alt"></i> Verify
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        function generateCertificate(type) {
            window.location.href = 'create.php?type=' + type;
        }

        function downloadCert(certId) {
            window.location.href = 'download.php?id=' + certId;
        }

        function verifyCert(hash) {
            alert('Blockchain Verification: ' + hash + '\n(Feature coming soon - integrates with blockchain for tamper-proof verification)');
        }
    </script>
</body>

</html>

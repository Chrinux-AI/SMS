<?php
/**
 * Mess Menu - Student Portal
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "Mess Menu";
$current_day = date('l');

include '../includes/cyber-nav.php';

// Sample menu data
$menu = [
    'Monday' => ['Breakfast' => 'Toast, Eggs, Tea', 'Lunch' => 'Rice, Chicken, Salad', 'Dinner' => 'Pasta, Vegetables'],
    'Tuesday' => ['Breakfast' => 'Pancakes, Juice', 'Lunch' => 'Jollof Rice, Fish', 'Dinner' => 'Beans, Plantain'],
    'Wednesday' => ['Breakfast' => 'Bread, Omelette', 'Lunch' => 'Fried Rice, Beef', 'Dinner' => 'Eba, Soup'],
    'Thursday' => ['Breakfast' => 'Cereal, Milk', 'Lunch' => 'Spaghetti, Meatballs', 'Dinner' => 'Yam, Egg Sauce'],
    'Friday' => ['Breakfast' => 'Toast, Beans', 'Lunch' => 'Coconut Rice, Chicken', 'Dinner' => 'Noodles, Vegetables'],
    'Saturday' => ['Breakfast' => 'Akara, Pap', 'Lunch' => 'Amala, Ewedu', 'Dinner' => 'Fried Rice, Salad'],
    'Sunday' => ['Breakfast' => 'Bread, Tea', 'Lunch' => 'Jollof Rice, Turkey', 'Dinner' => 'Porridge Yam']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <?php include '../includes/head-meta.php'; ?>
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-utensils"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Mess Menu</span>
            </div>
        </div>

        <div class="cyber-card" style="margin-bottom: 20px;">
            <div class="card-header">
                <h3><i class="fas fa-calendar-day"></i> Today's Menu - <?php echo $current_day; ?></h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <?php if (isset($menu[$current_day])): ?>
                        <?php foreach ($menu[$current_day] as $meal => $items): ?>
                            <div class="cyber-card" style="text-align: center; padding: 20px;">
                                <h4 style="color: var(--cyber-cyan);"><?php echo $meal; ?></h4>
                                <p style="margin-top: 10px;"><?php echo $items; ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-calendar-week"></i> Weekly Menu</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Breakfast</th>
                                <th>Lunch</th>
                                <th>Dinner</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menu as $day => $meals): ?>
                                <tr class="<?php echo $day === $current_day ? 'active' : ''; ?>">
                                    <td><strong><?php echo $day; ?></strong></td>
                                    <td><?php echo $meals['Breakfast']; ?></td>
                                    <td><?php echo $meals['Lunch']; ?></td>
                                    <td><?php echo $meals['Dinner']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/chatbot-unified.php'; ?>
</body>
</html>

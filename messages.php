<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';


require_login('login.php');

// Redirect to new enhanced chat system
header('Location: chat.php');
exit;

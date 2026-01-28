<?php
// Logout pagina
require_once 'config/config.php';

// Uitloggen
$auth->logout();

// Redirect naar login
header('Location: login.php');
exit;

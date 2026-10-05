<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$_SESSION = [];
session_destroy();
redirect(BASE_URL . '/admin/login.php');

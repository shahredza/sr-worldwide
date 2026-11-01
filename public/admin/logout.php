<?php
require __DIR__ . '/../../includes/bootstrap.php';

if (!is_post()) {
    redirect('admin/');
}
csrf_check();

unset($_SESSION['admin_id']);
session_regenerate_id(true);
redirect('admin/login.php');

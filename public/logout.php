<?php
require __DIR__ . '/../includes/bootstrap.php';

// Logout is a POST with a CSRF token, so other sites can't log people out.
if (!is_post()) {
    redirect('');
}
csrf_check();

unset($_SESSION['user_id'], $_SESSION['after_login']);
session_regenerate_id(true);
flash('success', 'You have been logged out.');
redirect('');

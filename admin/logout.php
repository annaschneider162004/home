<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
session_destroy();
redirect(ADMIN_URL . '/login.php');

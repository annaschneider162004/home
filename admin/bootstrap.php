<?php
/**
 * Admin bootstrap — load config + includes + define ADMIN_URL
 */
define('ADMIN_BOOTSTRAP', true);
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Admin URL (relative)
define('ADMIN_URL', rtrim(APP_URL, '/') . '/admin');

require_once __DIR__ . '/auth.php';

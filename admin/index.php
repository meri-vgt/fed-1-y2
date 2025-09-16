<?php
/**
 * Criminal Minds Blog - Admin Dashboard
 * Redirects to the main router in index.php
 */

require_once '../includes/functions.php';
header('Location: ' . getBaseUrl() . '/?page=admin');
exit;
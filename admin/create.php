<?php
/**
 * Criminal Minds Blog - Create New Post
 * Redirects to the main router in index.php
 */

require_once '../includes/functions.php';

$base = getBaseUrl();
header('Location: ' . $base . '/?page=admin-create');
exit; ?>
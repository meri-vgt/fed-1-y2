<?php
/**
 * Criminal Minds Blog - Post View
 * Redirects to the main router in index.php
 */

require_once 'includes/functions.php';
$base = getBaseUrl();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$preview = isset($_GET['preview']) ? '1' : '';
$url = $base . '/?page=post' . ($id ? ('&id=' . $id) : '') . ($preview ? '&preview=1' : '');
header('Location: ' . $url);
exit;
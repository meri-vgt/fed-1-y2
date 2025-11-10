<?php
/**
 * Criminal Minds Blog - Search
 * Redirects to the main router in index.php
 */

require_once 'includes/functions.php';
$base = getBaseUrl();
$q = isset($_GET['q']) ? (string)$_GET['q'] : '';
$url = $base . '/?page=search' . ($q !== '' ? ('&q=' . urlencode($q)) : '');
header('Location: ' . $url);
exit;
?>
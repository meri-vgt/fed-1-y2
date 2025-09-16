<?php
/**
 * Criminal Minds Blog - Public Create Post Page
 * Redirects to the main router in index.php
 */

require_once 'includes/functions.php';

$base = getBaseUrl();
header('Location: ' . $base . '/?page=create');
exit; ?>







<?php
/**
 * Criminal Minds Blog - Feature Post
 * Redirects to the main router in index.php
 */

require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . getBaseUrl() . '/?page=admin');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0) {
    setFeaturedPost($id);
}

header('Location: ' . getBaseUrl() . '/?page=admin&featured=' . urlencode((string)$id));
exit;




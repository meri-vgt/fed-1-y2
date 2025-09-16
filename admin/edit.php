<?php
/**
 * Criminal Minds Blog - Edit Post
 * Redirects to the public edit page
 */

require_once '../includes/functions.php';

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($postId) {
    header('Location: ' . getBaseUrl() . '/?page=edit&id=' . $postId);
} else {
    header('Location: ' . getBaseUrl() . '/');
}
exit;
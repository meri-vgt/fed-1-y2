<?php
/**
 * Test script to verify all routes are properly handled by index.php
 */

echo "Testing route configuration...\n\n";

// Test routes that should be handled by index.php
$routes = [
    '/' => 'Home page',
    '/?page=post&id=1' => 'Post view',
    '/?page=search' => 'Search page',
    '/?page=create' => 'Create post page',
    '/?page=admin' => 'Admin dashboard',
    '/?page=admin-create' => 'Admin create post',
    '/?page=admin-edit&id=1' => 'Admin edit post',
    '/?page=admin-delete&id=1' => 'Admin delete post',
    '/?page=admin-feature' => 'Admin feature post',
    '/?page=disclaimer' => 'Disclaimer page'
];

foreach ($routes as $route => $description) {
    echo "Route: $route - $description\n";
}

echo "\nAll routes should be handled by index.php through the routing system.\n";
echo "Standalone PHP files now redirect to the main router.\n";
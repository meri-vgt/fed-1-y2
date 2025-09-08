<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Criminal Minds</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-content">
            <div class="loading-logo">Criminal Minds</div>
            <div class="loading-spinner"></div>
            <div class="loading-text">Laden van misdaadgegevens...</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/">Criminal Minds</a>
            </div>
            <div class="nav-links">
                <a href="/" class="nav-link">Home</a>
                <a href="/admin/" class="nav-link">Admin</a>
                <div class="search-container">
                    <form action="/search.php" method="GET" class="search-form">
                        <input type="text" name="q" placeholder="Zoek in misdaadverslagen..." class="search-input" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
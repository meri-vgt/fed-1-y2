<?php
    // Determine base URL dynamically so assets work from subfolders (e.g., /fed-1-y2)
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $dirName = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $baseUrl = $dirName;
    // If we are in a nested directory like /fed-1-y2/admin, step up to project root
    if (preg_match('#/admin$#', $baseUrl)) {
        $baseUrl = rtrim(preg_replace('#/admin$#', '', $baseUrl), '/');
    }
    if ($baseUrl === '') { $baseUrl = '/'; }
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Criminal Minds</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>window.APP_BASE_URL = <?php echo json_encode($baseUrl); ?>;</script>
    <script src="<?php echo $baseUrl; ?>/js/main.js" defer></script>
    <script src="<?php echo $baseUrl; ?>/js/text-editor.js" defer></script>
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
                <a href="<?php echo $baseUrl; ?>/">Criminal Minds</a>
            </div>
            <div class="nav-links">
                <a href="<?php echo $baseUrl; ?>/" class="nav-link">Home</a>
                <a href="<?php echo $baseUrl; ?>/?page=admin" class="nav-link">Admin</a>
                <a href="<?php echo $baseUrl; ?>/?page=search" class="nav-link">Zoeken</a>
                <div class="search-container">
                    <form action="<?php echo $baseUrl; ?>/" method="GET" class="search-form">
                        <input type="hidden" name="page" value="search">
                        <input type="text" name="q" placeholder="Zoek in misdaadverslagen..." class="search-input" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
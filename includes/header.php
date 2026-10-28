<?php
require_once __DIR__ . '/functions.php';

$pageTitle = isset($pageTitle) ? $pageTitle : '';
$metaDescription = isset($metaDescription) ? $metaDescription : 'Astra is a field guide to the night sky: real stars, galaxies, nebulae, planets, and star clusters, each with verified facts and its own page.';
$fullTitle = $pageTitle === '' ? 'Astra' : $pageTitle . ' | Astra';
$searchValue = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription) ?>">
    <title><?= e($fullTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <header class="site-header">
        <div class="wrap">
            <div class="logo"><a href="index.php">Astr<span>a</span></a></div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="catalog.php">Catalog</a></li>
                    <li><a href="add.php">Add Object</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <form class="search" action="catalog.php" method="get" role="search">
                <input type="search" name="q" value="<?= e($searchValue) ?>" placeholder="Search the catalog" aria-label="Search the catalog">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>
    </header>

    <main<?= isset($mainClass) ? ' class="' . e($mainClass) . '"' : '' ?>>

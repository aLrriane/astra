<?php
require_once __DIR__ . '/includes/functions.php';

$categories = db()->query('SELECT * FROM ' . t('categories') . ' ORDER BY sort_order, id')->fetchAll();

$featured = db()->query(
    'SELECT o.*, c.label AS category_label
     FROM ' . t('objects') . ' o
     JOIN ' . t('categories') . ' c ON c.id = o.category_id
     WHERE o.featured = 1
     ORDER BY o.name
     LIMIT 3'
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>
    <section class="hero">
        <div class="wrap">
            <h1>Celestial Bodies</h1>
            <p>Supergiant stars, galaxies, and everything nice.</p>
            <a class="btn" href="catalog.php">View Full Catalog</a>
        </div>
    </section>

    <section class="glossary">
        <div class="wrap">
            <div class="section-label">Before you start</div>
            <h2 class="section-title">What's Out There</h2>
            <div class="glossary-grid">
                <?php foreach ($categories as $c): ?>
                    <a class="glossary-item" href="catalog.php#<?= e($c['slug']) ?>">
                        <h3><?= e($c['label']) ?> &rarr;</h3>
                        <p><?= e($c['definition']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="catalog">
        <div class="wrap">
            <div class="section-label">A few to start with</div>
            <h2 class="section-title">Featured Objects</h2>
            <p style="color: var(--text-muted); max-width: 60ch; margin: -14px 0 30px;">Three picks from the full catalog. See everything, sorted by category, on the catalog page.</p>

            <div class="catalog-grid">
                <?php foreach ($featured as $o) { render_card($o); } ?>
            </div>

            <div style="margin-top: 40px;">
                <a class="btn" href="catalog.php">View Full Catalog</a>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/includes/footer.php'; ?>

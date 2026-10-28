<?php
require_once __DIR__ . '/includes/functions.php';

$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$baseSelect = 'SELECT o.*, c.label AS category_label, c.name AS category_name, c.slug AS category_slug
               FROM ' . t('objects') . ' o
               JOIN ' . t('categories') . ' c ON c.id = o.category_id';

if ($q !== '') {

    $like = '%' . str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $q) . '%';

    $stmt = db()->prepare(
        'SELECT o.*, c.label AS category_label, c.name AS category_name, c.slug AS category_slug,
                ( (c.label = ? OR c.name = ?) * 20
                + (o.name LIKE ?) * 10
                + (c.label LIKE ? OR c.name LIKE ?) * 8
                + (o.teaser LIKE ?) * 3
                + (o.location LIKE ?) * 2
                + (o.description LIKE ?) * 1 ) AS score
         FROM ' . t('objects') . ' o
         JOIN ' . t('categories') . ' c ON c.id = o.category_id
         WHERE o.name LIKE ? OR o.location LIKE ? OR o.teaser LIKE ? OR o.description LIKE ?
            OR c.name LIKE ? OR c.label LIKE ?
            OR EXISTS (SELECT 1 FROM ' . t('facts') . ' f WHERE f.object_id = o.id AND f.value LIKE ?)
         ORDER BY score DESC, o.name'
    );
    $stmt->execute(array_merge(
        array($q, $q),                 
        array_fill(0, 6, $like),       
        array_fill(0, 7, $like)        
    ));
    $results = $stmt->fetchAll();
    $pageTitle = 'Search: ' . $q;
} else {

    $all = db()->query($baseSelect . ' ORDER BY c.sort_order, c.id, o.name')->fetchAll();
    $catDefs = db()->query('SELECT * FROM ' . t('categories') . ' ORDER BY sort_order, id')->fetchAll();
    $grouped = array();
    foreach ($all as $o) {
        $grouped[$o['category_id']][] = $o;
    }
    $pageTitle = 'Catalog';
}

$metaDescription = 'Browse the full Astra catalog of celestial objects, organized by category: stars, galaxies, nebulae, planets, and star clusters.';
include __DIR__ . '/includes/header.php';
?>
    <section class="catalog" style="padding-top: 60px;">
        <div class="wrap">

<?php if ($q !== ''): ?>
            <div class="section-label">Search</div>
            <h1 class="section-title">Results for &ldquo;<?= e($q) ?>&rdquo;</h1>
            <p style="color: var(--text-muted); max-width: 60ch; margin: -14px 0 40px;">
                <?= count($results) ?> <?= count($results) === 1 ? 'object' : 'objects' ?> found.
                <a href="catalog.php" style="color: var(--accent);">Show the full catalog</a>
            </p>

            <?php if ($results): ?>
                <div class="catalog-grid">
                    <?php foreach ($results as $o) { render_card($o); } ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Nothing matched that search. Try a shorter word, a constellation, or a category like &ldquo;galaxy&rdquo;.</p>
                </div>
            <?php endif; ?>

<?php else: ?>
            <div class="section-label">Everything on file</div>
            <h1 class="section-title">The Full Catalog</h1>
            <p style="color: var(--text-muted); max-width: 60ch; margin: -14px 0 40px;">
                <?= count($all) ?> objects across <?= count($grouped) ?> categories. Jump to a section, or scroll through all of them.
            </p>

            <?php foreach ($catDefs as $c): ?>
                <?php if (empty($grouped[$c['id']])) { continue; } ?>
                <div class="catalog-category" id="<?= e($c['slug']) ?>">
                    <div class="section-label"><?= count($grouped[$c['id']]) ?> in this category</div>
                    <h2 class="section-title"><?= e($c['name']) ?></h2>
                    <p class="category-def"><?= e($c['definition']) ?></p>
                    <div class="catalog-grid">
                        <?php foreach ($grouped[$c['id']] as $o) { render_card($o); } ?>
                    </div>
                </div>
            <?php endforeach; ?>
<?php endif; ?>

        </div>
    </section>
<?php include __DIR__ . '/includes/footer.php'; ?>

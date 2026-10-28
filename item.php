<?php
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = db()->prepare(
    'SELECT o.*, c.label AS category_label, c.name AS category_name, c.slug AS category_slug
     FROM ' . t('objects') . ' o
     JOIN ' . t('categories') . ' c ON c.id = o.category_id
     WHERE o.id = ?'
);
$stmt->execute(array($id));
$o = $stmt->fetch();

if (!$o) {
    http_response_code(404);
    $pageTitle = 'Not found';
    include __DIR__ . '/includes/header.php';
    ?>
    <section class="page-narrow">
        <div class="wrap">
            <h1 class="section-title">Object not found</h1>
            <p style="color: var(--text-muted);">That object isn't in the catalog. <a href="catalog.php" style="color: var(--accent);">Browse the full catalog</a>.</p>
        </div>
    </section>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = db()->prepare('SELECT label, value FROM ' . t('facts') . ' WHERE object_id = ? ORDER BY sort_order, id');
$stmt->execute(array($id));
$facts = $stmt->fetchAll();

$pageTitle = $o['name'];
$metaDescription = $o['name'] . ': ' . $o['teaser'];
$mainClass = 'object-detail';
include __DIR__ . '/includes/header.php';
?>
        <div class="wrap">
            <a class="back-link" href="catalog.php#<?= e($o['category_slug']) ?>">&larr; Back to <?= e($o['category_name']) ?></a>

            <div class="detail-layout">
                <div class="thumb">
                    <img src="<?= e($o['image']) ?>" alt="Illustration of <?= e($o['name']) ?>">
                </div>

                <div class="detail-info">
                    <span class="type-tag"><?= e($o['category_label']) ?></span>
                    <h1><?= e($o['name']) ?></h1>
                    <p class="location"><?= e($o['location']) ?></p>

                    <div class="description">
                        <?= paragraphs($o['description']) ?>
                        <?php if (!empty($o['source_url'])): ?>
                            <p class="source-link"><a href="<?= e($o['source_url']) ?>" target="_blank" rel="noopener">Source &rarr;</a></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($facts): ?>
                        <div class="fact-table">
                            <?php foreach ($facts as $f): ?>
                                <div><span><?= e($f['label']) ?></span><span><?= e($f['value']) ?></span></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="actions">
                        <a class="btn" href="edit.php?id=<?= (int)$o['id'] ?>">Edit</a>
                        <form action="delete.php" method="post" onsubmit="return confirm('Delete <?= e(addslashes($o['name'])) ?> from the catalog? This cannot be undone.');">
                            <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php include __DIR__ . '/includes/footer.php'; ?>

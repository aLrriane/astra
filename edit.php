<?php
require_once __DIR__ . '/includes/functions.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

$stmt = db()->prepare('SELECT * FROM ' . t('objects') . ' WHERE id = ?');
$stmt->execute(array($id));
$obj = $stmt->fetch();

if (!$obj) {
    http_response_code(404);
    $pageTitle = 'Not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="page-narrow"><div class="wrap"><h1 class="section-title">Object not found</h1>';
    echo '<p style="color: var(--text-muted);"><a href="catalog.php" style="color: var(--accent);">Back to the catalog</a></p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$categories = db()->query('SELECT id, label FROM ' . t('categories') . ' ORDER BY sort_order, id')->fetchAll();

$old = array(
    'name' => $obj['name'], 'category_id' => (int)$obj['category_id'], 'location' => $obj['location'],
    'teaser' => $obj['teaser'], 'description' => $obj['description'],
    'source_url' => (string)$obj['source_url'],
    'fact_label' => array('', '', ''), 'fact_value' => array('', '', ''),
);
$stmt = db()->prepare('SELECT label, value FROM ' . t('facts') . ' WHERE object_id = ? ORDER BY sort_order, id LIMIT 3');
$stmt->execute(array($id));
$i = 0;
foreach ($stmt->fetchAll() as $f) {
    $old['fact_label'][$i] = $f['label'];
    $old['fact_value'][$i] = $f['value'];
    $i++;
}

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && empty($_FILES) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $errors[] = 'That upload is larger than the server allows. Try a smaller image.';
    } else {
        $old['name']        = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $old['category_id'] = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $old['location']    = trim(isset($_POST['location']) ? $_POST['location'] : '');
        $old['teaser']      = trim(isset($_POST['teaser']) ? $_POST['teaser'] : '');
        $old['description'] = trim(isset($_POST['description']) ? $_POST['description'] : '');
        $old['source_url']  = trim(isset($_POST['source_url']) ? $_POST['source_url'] : '');
        for ($i = 0; $i < 3; $i++) {
            $old['fact_label'][$i] = trim(isset($_POST['fact_label_' . $i]) ? $_POST['fact_label_' . $i] : '');
            $old['fact_value'][$i] = trim(isset($_POST['fact_value_' . $i]) ? $_POST['fact_value_' . $i] : '');
            if (($old['fact_label'][$i] === '') !== ($old['fact_value'][$i] === '')) {
                $errors[] = 'Quick fact ' . ($i + 1) . ' needs both a label and a value (or leave both empty).';
            }
        }

        if ($old['name'] === '' || strlen($old['name']) > 150) {
            $errors[] = 'Enter a name (up to 150 characters).';
        }
        $validCategory = false;
        foreach ($categories as $c) {
            if ((int)$c['id'] === $old['category_id']) { $validCategory = true; }
        }
        if (!$validCategory) {
            $errors[] = 'Choose a category.';
        }
        if ($old['location'] === '' || strlen($old['location']) > 255) {
            $errors[] = 'Enter a location line (up to 255 characters).';
        }
        if ($old['teaser'] === '' || strlen($old['teaser']) > 500) {
            $errors[] = 'Enter a one-sentence teaser (up to 500 characters).';
        }
        if ($old['description'] === '') {
            $errors[] = 'Enter a description.';
        }
        if ($old['source_url'] !== '') {
            $scheme = strtolower((string)parse_url($old['source_url'], PHP_URL_SCHEME));
            if (!filter_var($old['source_url'], FILTER_VALIDATE_URL) || !in_array($scheme, array('http', 'https'), true)) {
                $errors[] = 'The source link must be a full web address starting with http:// or https://.';
            }
        }

        $newImage = null;
        $hasNewFile = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;
        if (!$errors && $hasNewFile) {
            $imageError = '';
            $newImage = save_uploaded_image($_FILES['image'], $imageError);
            if ($newImage === null) { $errors[] = $imageError; }
        } elseif ($errors && $hasNewFile) {
            $errors[] = 'Please choose the new image again after fixing the problems above.';
        }

        if (!$errors) {
            $pdo = db();
            try {
                $pdo->beginTransaction();
                $imageToSave = $newImage !== null ? $newImage : $obj['image'];
                $stmt = $pdo->prepare(
                    'UPDATE ' . t('objects') . '
                     SET category_id = ?, name = ?, location = ?, teaser = ?, description = ?, source_url = ?, image = ?
                     WHERE id = ?'
                );
                $stmt->execute(array(
                    $old['category_id'], $old['name'], $old['location'], $old['teaser'], $old['description'],
                    $old['source_url'] === '' ? null : $old['source_url'], $imageToSave, $id,
                ));

                $pdo->prepare('DELETE FROM ' . t('facts') . ' WHERE object_id = ?')->execute(array($id));
                $factStmt = $pdo->prepare('INSERT INTO ' . t('facts') . ' (object_id, label, value, sort_order) VALUES (?, ?, ?, ?)');
                for ($i = 0; $i < 3; $i++) {
                    if ($old['fact_label'][$i] !== '' && $old['fact_value'][$i] !== '') {
                        $factStmt->execute(array($id, $old['fact_label'][$i], $old['fact_value'][$i], $i));
                    }
                }
                $pdo->commit();


                if ($newImage !== null && strpos($obj['image'], 'images/uploads/') === 0) {
                    @unlink(__DIR__ . '/' . $obj['image']);
                }
                header('Location: item.php?id=' . $id);
                exit;
            } catch (PDOException $ex) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                if ($newImage !== null) { @unlink(__DIR__ . '/' . $newImage); }
                $errors[] = 'The changes could not be saved.' . (DEBUG ? ' ' . $ex->getMessage() : '');
            }
        }
    }
}

$pageTitle = 'Edit ' . $obj['name'];
$metaDescription = 'Edit an object in the Astra catalog.';
$mainClass = 'page';
include __DIR__ . '/includes/header.php';
?>
        <div class="wrap">
            <h1>Edit <?= e($obj['name']) ?></h1>
            <p>Change any detail below and save. Leave the image box empty to keep the current picture.</p>

            <?php if ($errors): ?>
                <div class="alert" role="alert">
                    <strong>Fix the following and try again:</strong>
                    <ul>
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="form" action="edit.php?id=<?= (int)$id ?>" method="post" enctype="multipart/form-data">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" maxlength="150" value="<?= e($old['name']) ?>" required>

                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"<?= (int)$c['id'] === (int)$old['category_id'] ? ' selected' : '' ?>><?= e($c['label']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="location">Location line</label>
                <input type="text" id="location" name="location" maxlength="255" value="<?= e($old['location']) ?>" required>

                <label for="teaser">Teaser</label>
                <input type="text" id="teaser" name="teaser" maxlength="500" value="<?= e($old['teaser']) ?>" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" required><?= e($old['description']) ?></textarea>
                <p class="hint">Press Enter between paragraphs to start a new one.</p>

                <label for="source_url">Source link (optional)</label>
                <input type="url" id="source_url" name="source_url" maxlength="500" value="<?= e($old['source_url']) ?>">

                <fieldset>
                    <legend>Quick facts (up to three)</legend>
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="fact-row">
                            <input type="text" name="fact_label_<?= $i ?>" maxlength="100" placeholder="Label, e.g. Distance" aria-label="Fact <?= $i + 1 ?> label" value="<?= e($old['fact_label'][$i]) ?>">
                            <input type="text" name="fact_value_<?= $i ?>" maxlength="255" placeholder="Value, e.g. 2.5 million light-years" aria-label="Fact <?= $i + 1 ?> value" value="<?= e($old['fact_value'][$i]) ?>">
                        </div>
                    <?php endfor; ?>
                </fieldset>

                <label>Current image</label>
                <div class="current-image"><img src="<?= e($obj['image']) ?>" alt="Current image of <?= e($obj['name']) ?>"></div>

                <label for="image">Replace image (optional)</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <p class="hint">JPG, PNG, GIF or WebP, up to 5 MB.</p>

                <button type="submit" class="btn">Save changes</button>
                <a class="cancel-link" href="item.php?id=<?= (int)$id ?>">Cancel</a>
            </form>
        </div>
<?php include __DIR__ . '/includes/footer.php'; ?>

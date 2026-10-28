<?php
require_once __DIR__ . '/includes/functions.php';

$categories = db()->query('SELECT id, label FROM ' . t('categories') . ' ORDER BY sort_order, id')->fetchAll();

$errors = array();
$old = array(
    'name' => '', 'category_id' => '', 'location' => '', 'teaser' => '',
    'description' => '', 'source_url' => '',
    'fact_label' => array('', '', ''), 'fact_value' => array('', '', ''),
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    if (empty($_POST) && empty($_FILES) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $errors[] = 'That upload is larger than the server allows. Try a smaller image.';
    } else {
        $old['name']        = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $old['category_id'] = isset($_POST['category_id']) ? (int)$_POST['category_id'] : '';
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



        $imageError = '';
        $imagePath = null;
        if (!$errors) {
            $imagePath = save_uploaded_image(isset($_FILES['image']) ? $_FILES['image'] : array(), $imageError);
            if ($imagePath === null) {
                $errors[] = $imageError;
            }
        } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Please choose an image.';
        } else {
            $errors[] = 'Please choose your image again after fixing the problems above.';
        }



        if (!$errors) {
            $pdo = db();
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare(
                    'INSERT INTO ' . t('objects') . ' (category_id, name, location, teaser, description, source_url, image)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute(array(
                    $old['category_id'], $old['name'], $old['location'], $old['teaser'],
                    $old['description'], $old['source_url'] === '' ? null : $old['source_url'], $imagePath,
                ));
                $newId = (int)$pdo->lastInsertId();

                $factStmt = $pdo->prepare('INSERT INTO ' . t('facts') . ' (object_id, label, value, sort_order) VALUES (?, ?, ?, ?)');
                for ($i = 0; $i < 3; $i++) {
                    if ($old['fact_label'][$i] !== '' && $old['fact_value'][$i] !== '') {
                        $factStmt->execute(array($newId, $old['fact_label'][$i], $old['fact_value'][$i], $i));
                    }
                }
                $pdo->commit();

                header('Location: item.php?id=' . $newId);
                exit;
            } catch (PDOException $ex) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                if ($imagePath) { @unlink(__DIR__ . '/' . $imagePath); }
                $errors[] = 'The object could not be saved.' . (DEBUG ? ' ' . $ex->getMessage() : '');
            }
        }
    }
}

$pageTitle = 'Add Object';
$metaDescription = 'Add a new celestial object to the Astra catalog.';
$mainClass = 'page';
include __DIR__ . '/includes/header.php';
?>
        <div class="wrap">
            <h1>Add an Object</h1>
            <p>Know a star, planet, or cluster that belongs in the catalog? Fill in the details and it will appear on the catalog page right away.</p>

            <?php if ($errors): ?>
                <div class="alert" role="alert">
                    <strong>Fix the following and try again:</strong>
                    <ul>
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="form" action="add.php" method="post" enctype="multipart/form-data">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" maxlength="150" value="<?= e($old['name']) ?>" required>

                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Choose a category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"<?= (int)$c['id'] === $old['category_id'] ? ' selected' : '' ?>><?= e($c['label']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="location">Location line</label>
                <input type="text" id="location" name="location" maxlength="255" placeholder="Constellation Lyra &middot; 2,500 light-years away" value="<?= e($old['location']) ?>" required>

                <label for="teaser">Teaser</label>
                <input type="text" id="teaser" name="teaser" maxlength="500" placeholder="One sentence shown on the catalog card" value="<?= e($old['teaser']) ?>" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" required><?= e($old['description']) ?></textarea>
                <p class="hint">Press Enter between paragraphs to start a new one.</p>

                <label for="source_url">Source link (optional)</label>
                <input type="url" id="source_url" name="source_url" maxlength="500" placeholder="https://en.wikipedia.org/wiki/..." value="<?= e($old['source_url']) ?>">

                <fieldset>
                    <legend>Quick facts (optional, up to three)</legend>
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="fact-row">
                            <input type="text" name="fact_label_<?= $i ?>" maxlength="100" placeholder="Label, e.g. Distance" aria-label="Fact <?= $i + 1 ?> label" value="<?= e($old['fact_label'][$i]) ?>">
                            <input type="text" name="fact_value_<?= $i ?>" maxlength="255" placeholder="Value, e.g. 2.5 million light-years" aria-label="Fact <?= $i + 1 ?> value" value="<?= e($old['fact_value'][$i]) ?>">
                        </div>
                    <?php endfor; ?>
                </fieldset>

                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                <p class="hint">JPG, PNG, GIF or WebP, up to 5 MB.</p>

                <button type="submit" class="btn">Add to catalog</button>
            </form>
        </div>
<?php include __DIR__ . '/includes/footer.php'; ?>

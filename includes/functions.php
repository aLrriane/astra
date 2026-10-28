<?php
require_once __DIR__ . '/../config.php';

/* ---------- friendly error page ---------- */
set_exception_handler(function ($e) {
    http_response_code(500);
    echo '<!DOCTYPE html><meta charset="UTF-8"><title>Error | Astra</title>';
    echo '<body style="font-family:sans-serif;background:#14121b;color:#f1e9dc;padding:40px">';
    echo '<h1>Something went wrong</h1>';
    echo '<p>The database could not be reached or a table is missing. ';
    echo 'Check <code>config.php</code>, and make sure you have run <code>setup.php</code> once.</p>';
    if (defined('DEBUG') && DEBUG) {
        echo '<pre style="white-space:pre-wrap;color:#d8a73d">' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
    }
    echo '</body>';
});

/* ---------- database ---------- */
function db()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/* Table name with the student-ID prefix, e.g. t('objects') => `211400_astra_objects` */
function t($name)
{
    return '`' . STUDENT_ID . '_astra_' . $name . '`';
}

/* ---------- output helpers ---------- */
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/* Turn a block of text into <p> paragraphs (one per line break) */
function paragraphs($text)
{
    $html = '';
    foreach (preg_split('/\R+/', trim((string)$text)) as $p) {
        $p = trim($p);
        if ($p !== '') {
            $html .= '<p>' . e($p) . '</p>' . "\n";
        }
    }
    return $html;
}

/* One catalog card; $o needs: id, image, name, location, teaser, category_label */
function render_card(array $o)
{
    ?>
    <a class="object-card" href="item.php?id=<?= (int)$o['id'] ?>">
        <div class="thumb"><img src="<?= e($o['image']) ?>" alt="Illustration of <?= e($o['name']) ?>" loading="lazy"></div>
        <span class="type-tag"><?= e($o['category_label']) ?></span>
        <h3><?= e($o['name']) ?></h3>
        <p class="location"><?= e($o['location']) ?></p>
        <p class="teaser"><?= e($o['teaser']) ?></p>
    </a>
    <?php
}

/* ---------- image upload ----------
 * Returns the saved path (e.g. "images/uploads/ab12.jpg") or null, and fills $error.
 */
function save_uploaded_image($file, &$error)
{
    $maxBytes = 5 * 1024 * 1024; // 5 MB

    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $error = 'Please choose an image.';
        return null;
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = 'That image is too large. Please use one under 5 MB.';
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'The image could not be uploaded. Please try again.';
        return null;
    }
    if ($file['size'] > $maxBytes) {
        $error = 'That image is too large. Please use one under 5 MB.';
        return null;
    }

    // Check it is really an image (not just named like one)
    $info = @getimagesize($file['tmp_name']);
    $types = array(
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
    );
    if (defined('IMAGETYPE_WEBP')) {
        $types[IMAGETYPE_WEBP] = 'webp';
    }
    if ($info === false || !isset($types[$info[2]])) {
        $error = 'The file must be a JPG, PNG, GIF or WebP image.';
        return null;
    }

    $dir = dirname(__DIR__) . '/images/uploads';
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        $error = 'The images/uploads folder could not be created. Create it manually and make it writable.';
        return null;
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $types[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
        $error = 'The image could not be saved. Make sure images/uploads is writable (permissions 755 or 777).';
        return null;
    }
    return 'images/uploads/' . $filename;
}

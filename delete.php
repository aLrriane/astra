<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: catalog.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$pdo = db();

$stmt = $pdo->prepare('SELECT image FROM ' . t('objects') . ' WHERE id = ?');
$stmt->execute(array($id));
$obj = $stmt->fetch();

if ($obj) {
    $pdo->beginTransaction();
    $pdo->prepare('DELETE FROM ' . t('facts') . ' WHERE object_id = ?')->execute(array($id));
    $pdo->prepare('DELETE FROM ' . t('objects') . ' WHERE id = ?')->execute(array($id));
    $pdo->commit();


    if (strpos($obj['image'], 'images/uploads/') === 0) {
        @unlink(__DIR__ . '/' . $obj['image']);
    }
}

header('Location: catalog.php');
exit;

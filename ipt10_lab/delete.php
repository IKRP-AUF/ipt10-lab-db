<?php
require_once 'db_connect.php';
require_once 'validation.php';

// id arrives in the URL on the confirm screen (GET) and in a hidden field on the POST
$id = trim($_POST['id'] ?? $_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

$pageTitle = 'Delete Student (mysqli)';
$versionLabel = 'mysqli version';

// Step 1: look up the student so the confirmation screen can name them
// TODO(18): prepared SELECT of first_name, last_name by id
$s = $conn->prepare('SELECT first_name, last_name FROM students WHERE id = ?');
$s->bind_param('s', $id);
$s->execute();
$r = $s->get_result()->fetch_assoc();
$s->close();

require 'header.php';

if (!$r) {
    echo '<div class="alert alert-warning">Student not found.</div><a href="index.php" class="btn btn-outline-secondary">Back to list</a>';
    $conn->close();
    require 'footer.php';
    exit;
}

// Step 2: delete ONLY on a POST (GET just shows the confirmation)
// TODO(19): POST performs the delete; affected_rows === 1 proves a row was removed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = $conn->prepare('DELETE FROM students WHERE id = ?');
    $d->bind_param('s', $id);
    $d->execute();

    if ($d->affected_rows === 1) {
        echo '<div class="alert alert-success">Student deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to delete student.</div>';
    }
    $d->close();
    echo '<a href="index.php" class="btn btn-primary">Back to list</a>';
    $conn->close();
} else {
    // TODO(20): confirmation screen: name, hidden id, Yes/Cancel, link to index.php
    ?>
    <h2 class="mb-3">Delete Student</h2>
    <div class="card border-danger shadow-sm"><div class="card-body">
      <p class="mb-3">Are you sure you want to delete
        <strong><?= e($r['first_name'] . ' ' . $r['last_name']) ?></strong>? This cannot be undone.</p>
      <form method="POST" class="d-flex gap-2">
        <input type="hidden" name="id" value="<?= e($id) ?>">
        <button type="submit" class="btn btn-danger">Yes, delete</button>
        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
      </form>
    </div></div>
    <?php
    $conn->close();
}
require 'footer.php';

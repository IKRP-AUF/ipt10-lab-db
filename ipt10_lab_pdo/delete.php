<?php
require_once 'config.php';
require_once 'validation.php';

$id = trim($_POST['id'] ?? $_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

$pageTitle = 'Delete Student (PDO)';
$versionLabel = 'PDO version';

// Step 1: confirm screen data
$stmt = $pdo->prepare('SELECT first_name, last_name FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

require 'header.php';

if (!$student) {   // not-found case
    echo '<div class="alert alert-warning">Student not found.</div><a href="index.php" class="btn btn-outline-secondary">Back to list</a>';
    require 'footer.php';
    exit;
}

// Step 2: deletion, only after the POST confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$pdo->inTransaction()) { $pdo->beginTransaction(); }   // autocommit is OFF: a prior SELECT may already have opened one
        $d = $pdo->prepare('DELETE FROM students WHERE id = ?');
        $d->execute([$id]);
        if ($d->rowCount() === 1) {
            $pdo->commit();
            echo '<div class="alert alert-success">Student deleted successfully.</div>';
        } else {
            $pdo->rollBack();
            echo '<div class="alert alert-danger">Failed to delete student.</div>';
        }
    } catch (PDOException $ex) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        error_log('Delete failed: ' . $ex->getMessage());
        echo '<div class="alert alert-danger">Failed to delete student.</div>';
    }
    echo '<a href="index.php" class="btn btn-primary">Back to list</a>';
} else {
?>
<h2 class="mb-3">Delete Student</h2>
<div class="card border-danger shadow-sm"><div class="card-body">
  <p class="mb-3">Are you sure you want to delete
    <strong><?= e($student['first_name'] . ' ' . $student['last_name']) ?></strong>? This cannot be undone.</p>
  <form method="POST" class="d-flex gap-2">
    <input type="hidden" name="id" value="<?= e($id) ?>">
    <button type="submit" class="btn btn-danger">Yes, delete</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
  </form>
</div></div>
<?php
}
require 'footer.php';

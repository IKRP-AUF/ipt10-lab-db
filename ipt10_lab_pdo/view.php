<?php
require_once 'config.php';
require_once 'validation.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);      // no bind_param() in PDO
$row = $stmt->fetch();

$pageTitle = 'Student Details (PDO)';
$versionLabel = 'PDO version';
require 'header.php';

if (!$row) {
    echo '<div class="alert alert-warning">Student not found.</div>';
} else {
    $fullName = preg_replace('/\s+/', ' ', trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']));
?>
<h2 class="mb-3">Student Details</h2>
<div class="card shadow-sm"><div class="card-body">
  <dl class="row mb-0">
    <dt class="col-sm-3">Student ID</dt><dd class="col-sm-9"><?= e($row['id']) ?></dd>
    <dt class="col-sm-3">Full Name</dt><dd class="col-sm-9"><?= e($fullName) ?></dd>
    <dt class="col-sm-3">Birthday</dt><dd class="col-sm-9"><?= e($row['birthday']) ?></dd>
    <dt class="col-sm-3">Sex</dt><dd class="col-sm-9"><?= e($row['sex']) ?></dd>
    <dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?= e($row['email']) ?></dd>
    <dt class="col-sm-3">Student Number</dt><dd class="col-sm-9"><?= e($row['student_number']) ?></dd>
    <dt class="col-sm-3">Program</dt><dd class="col-sm-9"><?= e($row['program']) ?></dd>
    <dt class="col-sm-3">Enrolment Date</dt><dd class="col-sm-9"><?= e($row['enrolment_date']) ?></dd>
    <dt class="col-sm-3">Created At</dt><dd class="col-sm-9"><?= e($row['created_at']) ?></dd>
    <dt class="col-sm-3">Updated At</dt><dd class="col-sm-9"><?= e($row['updated_at']) ?></dd>
  </dl>
</div></div>
<a href="index.php" class="btn btn-outline-secondary mt-3">Back to list</a>
<?php
}
require 'footer.php';

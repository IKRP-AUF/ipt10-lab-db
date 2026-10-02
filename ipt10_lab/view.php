<?php
require_once 'db_connect.php';
require_once 'validation.php';

// UUID string from the URL: trim, reject empty, never cast to int.
$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

// TODO(7): ONE placeholder, every column the detail page displays
$stmt = $conn->prepare(
    'SELECT id, first_name, middle_name, last_name, birthday, sex, email,
            student_number, program, enrolment_date, created_at, updated_at
     FROM students WHERE id = ?'
);
$stmt->bind_param('s', $id);   // UUID is text -> 's'
$stmt->execute();
// TODO(8): get_result() first, then fetch ONE associative row
$res = $stmt->get_result();
$row = $res->fetch_assoc();

$pageTitle = 'Student Details (mysqli)';
$versionLabel = 'mysqli version';
require 'header.php';

if (!$row) {
    echo '<div class="alert alert-warning">Student not found.</div>';
} else {
    // TODO(9): every field goes through htmlspecialchars() (via e())
    $fullName = trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']);
    $fullName = preg_replace('/\s+/', ' ', $fullName);
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
$stmt->close();
$conn->close();
require 'footer.php';

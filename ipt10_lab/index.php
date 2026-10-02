<?php
require_once 'db_connect.php';
require_once 'validation.php';

// TODO(4): static SQL, no user input, so no placeholders needed
$sql = 'SELECT id, first_name, last_name, email, enrolment_date
        FROM students
        ORDER BY enrolment_date DESC, id DESC';
$result = mysqli_query($conn, $sql);

$pageTitle = 'All Students (mysqli)';
$versionLabel = 'mysqli version';
require 'header.php';
?>
<h2 class="mb-3">All Student Records</h2>
<div class="table-responsive bg-white rounded shadow-sm">
<table class="table table-striped table-hover align-middle mb-0">
  <thead class="table-dark">
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Enrolled</th><th>Actions</th></tr>
  </thead>
  <tbody>
  <?php // TODO(5): mysqli_fetch_assoc() returns ['col' => value] and false at the end ?>
  <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td class="small text-muted"><?= e($row['id']) ?></td>
      <td><?= e($row['first_name'] . ' ' . $row['last_name']) ?></td>
      <td><?= e($row['email']) ?></td>
      <td><?= e($row['enrolment_date']) ?></td>
      <td>
        <a class="btn btn-sm btn-info" href="view.php?id=<?= urlencode($row['id']) ?>">View</a>
        <a class="btn btn-sm btn-warning" href="edit.php?id=<?= urlencode($row['id']) ?>">Edit</a>
        <a class="btn btn-sm btn-danger" href="delete.php?id=<?= urlencode($row['id']) ?>">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php
// TODO(6): free the result set FIRST, then close the connection
mysqli_free_result($result);
mysqli_close($conn);
require 'footer.php';

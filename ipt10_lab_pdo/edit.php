<?php
require_once 'config.php';
require_once 'validation.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

$stmt = $pdo->prepare(
    'SELECT first_name, middle_name, last_name, birthday, sex, email,
            student_number, program, enrolment_date
     FROM students WHERE id = ?'
);
$stmt->execute([$id]);
$row = $stmt->fetch();

$pageTitle = 'Edit Student (PDO)';
$versionLabel = 'PDO version';

if (!$row) {
    require 'header.php';
    echo '<div class="alert alert-warning">Student not found.</div><a href="index.php" class="btn btn-outline-secondary">Back to list</a>';
    require 'footer.php';
    exit;
}

$errors  = [];
$message = '';
$msgType = 'success';
$data = collect_student_input($row);   // GET: pre-filled form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data   = collect_student_input($_POST);
    $errors = validate_student($data);

    if (empty($errors)) {
        $d = normalise_for_db($data);
        $sql = 'UPDATE students
                SET first_name = ?, middle_name = ?, last_name = ?,
                    birthday = ?, sex = ?, email = ?, student_number = ?,
                    program = ?, enrolment_date = ?
                WHERE id = ?';
        try {
            if (!$pdo->inTransaction()) { $pdo->beginTransaction(); }   // autocommit is OFF: a prior SELECT may already have opened one
            $u = $pdo->prepare($sql);
            // The id (used in WHERE) must be the LAST element: it binds the final "?".
            $u->execute([
                $d['first_name'], $d['middle_name'], $d['last_name'], $d['birthday'], $d['sex'],
                $d['email'], $d['student_number'], $d['program'], $d['enrolment_date'], $id,
            ]);
            // rowCount() counts CHANGED rows. 0 = identical values (or no matching id), not an error.
            $changed = $u->rowCount();
            $pdo->commit();
            if ($changed > 0) {
                $message = 'Student updated successfully!';
            } else {
                $message = 'No changes were made (the submitted values are identical to the stored ones).';
                $msgType = 'info';
            }
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            if (($ex->errorInfo[1] ?? 0) === 1062) {
                $f = duplicate_field($ex->getMessage());
                $errors[$f] = ucfirst(str_replace('_', ' ', $f)) . ' already exists.';
            } else {
                error_log('Update failed: ' . $ex->getMessage());
                $errors['form'] = 'Could not update the student. Please try again.';
            }
        }
    }
}

require 'header.php';
?>
<h2 class="mb-3">Edit Student</h2>
<?php if ($message): ?><div class="alert alert-<?= $msgType ?>"><?= e($message) ?></div><?php endif; ?>
<?php $submitLabel = 'Save Changes'; require '_form.php'; ?>
<?php require 'footer.php';

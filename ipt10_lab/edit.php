<?php
require_once 'db_connect.php';
require_once 'validation.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

// Fetch the existing row (prepared SELECT, one row)
$stmt = $conn->prepare(
    'SELECT first_name, middle_name, last_name, birthday, sex, email,
            student_number, program, enrolment_date
     FROM students WHERE id = ?'
);
$stmt->bind_param('s', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pageTitle = 'Edit Student (mysqli)';
$versionLabel = 'mysqli version';

if (!$row) {
    require 'header.php';
    echo '<div class="alert alert-warning">Student not found.</div><a href="index.php" class="btn btn-outline-secondary">Back to list</a>';
    $conn->close();
    require 'footer.php';
    exit;
}

$errors  = [];
$message = '';
$msgType = 'success';
$data = collect_student_input($row);   // pre-fill from DB (NULL middle_name -> '')

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO(15): collect + trim, then the SAME validation as create.php
    $data   = collect_student_input($_POST);
    $errors = validate_student($data);

    if (empty($errors)) {
        $d = normalise_for_db($data);
        try {
            // TODO(16): UPDATE all editable columns, WHERE id = ? (10 placeholders)
            $u = $conn->prepare(
                'UPDATE students
                 SET first_name = ?, middle_name = ?, last_name = ?, birthday = ?, sex = ?,
                     email = ?, student_number = ?, program = ?, enrolment_date = ?
                 WHERE id = ?'
            );
            // ten strings (the UUID is a string too) -> 'ssssssssss'
            $u->bind_param(
                'ssssssssss',
                $d['first_name'], $d['middle_name'], $d['last_name'], $d['birthday'], $d['sex'],
                $d['email'], $d['student_number'], $d['program'], $d['enrolment_date'], $id
            );
            $u->execute();

            // TODO(17): execute() success != effect. affected_rows tells the difference:
            //   1 => row changed.  0 => nothing changed (identical values) OR no row matched.
            if ($u->affected_rows > 0) {
                $message = 'Student updated successfully!';
            } else {
                $message = 'No changes were made (the submitted values are identical to the stored ones).';
                $msgType = 'info';
            }
            $u->close();
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
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
<?php
$conn->close();
require 'footer.php';

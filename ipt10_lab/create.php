<?php
require_once 'db_connect.php';
require_once 'validation.php';

$errors  = [];
$success = '';
$data    = collect_student_input([]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = collect_student_input($_POST);   // trims every field
    // TODO(10)-(12): first/last name, email, birthday, sex rules live in validate_student()
    //   names:    $d === '' || strlen < 2 || strlen > 100 || !preg_match('/^[A-Za-z\s]+$/')
    //   email:    !filter_var($email, FILTER_VALIDATE_EMAIL)
    //   birthday: !preg_match('/^\d{4}-\d{2}-\d{2}$/')   (+ checkdate)
    //   sex:      !in_array($sex, ['Male', 'Female'], true)
    $errors = validate_student($data);

    if (empty($errors)) {
        $d = normalise_for_db($data);
        try {
            // TODO(13): id = UUID() in SQL; the other 9 columns are placeholders
            $stmt = $conn->prepare(
                'INSERT INTO students
                   (id, first_name, middle_name, last_name, birthday, sex, email,
                    student_number, program, enrolment_date)
                 VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            // TODO(14): nine strings -> 'sssssssss'
            $stmt->bind_param(
                'sssssssss',
                $d['first_name'], $d['middle_name'], $d['last_name'], $d['birthday'],
                $d['sex'], $d['email'], $d['student_number'], $d['program'], $d['enrolment_date']
            );
            $stmt->execute();

            // No insert_id with a UUID key -> confirm with affected_rows
            if ($stmt->affected_rows === 1) {
                $success = 'Student created successfully!';
                $data = collect_student_input([]);   // clear the form
            } else {
                $errors['form'] = 'Insert did not affect any row.';
            }
            $stmt->close();
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {           // duplicate UNIQUE value
                $f = duplicate_field($ex->getMessage());
                $errors[$f] = ucfirst(str_replace('_', ' ', $f)) . ' already exists.';
            } else {
                error_log('Create failed: ' . $ex->getMessage());
                $errors['form'] = 'Could not save the student. Please try again.';
            }
        }
    }
}

$pageTitle = 'Add Student (mysqli)';
$versionLabel = 'mysqli version';
require 'header.php';
?>
<h2 class="mb-3">Add New Student</h2>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php $submitLabel = 'Create Student'; require '_form.php'; ?>
<?php
$conn->close();
require 'footer.php';

<?php
require_once 'config.php';
require_once 'validation.php';

$errors  = [];
$success = '';
$data    = collect_student_input([]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data   = collect_student_input($_POST);
    $errors = validate_student($data);   // identical validation to Part 1

    if (empty($errors)) {
        $d = normalise_for_db($data);
        $sql = 'INSERT INTO students
                  (id, first_name, middle_name, last_name, birthday, sex, email,
                   student_number, program, enrolment_date)
                VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        // Order MUST match the placeholders in the SQL above.
        $values = [
            $d['first_name'], $d['middle_name'], $d['last_name'], $d['birthday'],
            $d['sex'], $d['email'], $d['student_number'], $d['program'], $d['enrolment_date'],
        ];
        try {
            if (!$pdo->inTransaction()) { $pdo->beginTransaction(); }   // autocommit is OFF: a prior SELECT may already have opened one            // autocommit is OFF (config.php)
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            if ($stmt->rowCount() === 1) {       // no lastInsertId() with a UUID key
                $pdo->commit();
                $success = 'Student created successfully!';
                $data = collect_student_input([]);
            } else {
                $pdo->rollBack();
                $errors['form'] = 'Insert did not affect any row.';
            }
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            if (($ex->errorInfo[1] ?? 0) === 1062) {   // duplicate UNIQUE value
                $f = duplicate_field($ex->getMessage());
                $errors[$f] = ucfirst(str_replace('_', ' ', $f)) . ' already exists.';
            } else {
                error_log('Create failed: ' . $ex->getMessage());
                $errors['form'] = 'Could not save the student. Please try again.';
            }
        }
    }
}

$pageTitle = 'Add Student (PDO)';
$versionLabel = 'PDO version';
require 'header.php';
?>
<h2 class="mb-3">Add New Student</h2>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php $submitLabel = 'Create Student'; require '_form.php'; ?>
<?php require 'footer.php';

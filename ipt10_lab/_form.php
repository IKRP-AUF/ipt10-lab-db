<?php
/* Shared form partial. Expects: $data (array), $errors (array), $submitLabel (string). */
function render_input(string $name, string $label, string $type, array $data, array $errors, bool $required = false, string $placeholder = ''): void
{
    $invalid = isset($errors[$name]) ? ' is-invalid' : '';
    echo '<div class="col-md-6">';
    echo '<label class="form-label" for="' . e($name) . '">' . e($label) . ($required ? ' <span class="text-danger">*</span>' : '') . '</label>';
    echo '<input class="form-control' . $invalid . '" type="' . e($type) . '" id="' . e($name) . '" name="' . e($name) . '"'
       . ' value="' . e($data[$name] ?? '') . '" placeholder="' . e($placeholder) . '">';
    if (isset($errors[$name])) {
        echo '<div class="text-danger small mt-1">' . e($errors[$name]) . '</div>';
    }
    echo '</div>';
}
?>
<?php if (!empty($errors['form'])): ?>
  <div class="alert alert-danger"><?= e($errors['form']) ?></div>
<?php endif; ?>
<form method="POST" class="row g-3 bg-white p-4 rounded shadow-sm" novalidate>
  <?php
  render_input('first_name', 'First Name', 'text', $data, $errors, true);
  render_input('middle_name', 'Middle Name', 'text', $data, $errors);
  render_input('last_name', 'Last Name', 'text', $data, $errors, true);
  // type="text" (not "date") so server-side validation can be demonstrated with bad dates
  render_input('birthday', 'Birthday (YYYY-MM-DD)', 'text', $data, $errors, true, 'YYYY-MM-DD');
  ?>
  <div class="col-md-6">
    <label class="form-label" for="sex">Sex <span class="text-danger">*</span></label>
    <select class="form-select<?= isset($errors['sex']) ? ' is-invalid' : '' ?>" id="sex" name="sex">
      <option value="">-- Select --</option>
      <?php foreach (['Male', 'Female'] as $opt): ?>
        <option value="<?= $opt ?>" <?= ($data['sex'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['sex'])): ?><div class="text-danger small mt-1"><?= e($errors['sex']) ?></div><?php endif; ?>
  </div>
  <?php
  render_input('email', 'Email', 'text', $data, $errors, true, 'name@school.edu');
  render_input('student_number', 'Student Number', 'text', $data, $errors, false, 'e.g. STU004 (blank = auto-generated)');
  render_input('program', 'Program', 'text', $data, $errors);
  render_input('enrolment_date', 'Enrolment Date (YYYY-MM-DD)', 'text', $data, $errors, true, 'YYYY-MM-DD');
  ?>
  <div class="col-12 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
    <a href="index.php" class="btn btn-outline-secondary">Back to list</a>
  </div>
</form>

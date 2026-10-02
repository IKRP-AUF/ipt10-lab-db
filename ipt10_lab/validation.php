<?php
declare(strict_types=1);

/** Escape a value for safe output in HTML. */
function e($v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Collect and trim every editable field from a POST array. */
function collect_student_input(array $post): array
{
    $fields = ['first_name', 'middle_name', 'last_name', 'birthday', 'sex',
               'email', 'student_number', 'program', 'enrolment_date'];
    $out = [];
    foreach ($fields as $f) {
        $out[$f] = trim((string)($post[$f] ?? ''));
    }
    return $out;
}

/** YYYY-MM-DD pattern AND a real calendar date. */
function valid_date(string $d): bool
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
        return false;
    }
    [$y, $m, $day] = array_map('intval', explode('-', $d));
    return checkdate($m, $day, $y);
}

/** Server-side validation (Chapter 5 rules). Returns [field => message]. */
function validate_student(array $d): array
{
    $e = [];

    foreach (['first_name' => 'First name', 'last_name' => 'Last name'] as $k => $label) {
        $len = mb_strlen($d[$k]);
        if ($d[$k] === '') {
            $e[$k] = "$label is required.";
        } elseif ($len < 2 || $len > 100) {
            $e[$k] = "$label must be 2-100 characters.";
        } elseif (!preg_match('/^[A-Za-z\s]+$/', $d[$k])) {
            $e[$k] = "$label may contain letters and spaces only.";
        }
    }

    // Middle name is optional, but if given it follows the same character rule.
    if ($d['middle_name'] !== '') {
        if (mb_strlen($d['middle_name']) > 100) {
            $e['middle_name'] = 'Middle name must be at most 100 characters.';
        } elseif (!preg_match('/^[A-Za-z\s]+$/', $d['middle_name'])) {
            $e['middle_name'] = 'Middle name may contain letters and spaces only.';
        }
    }

    if ($d['email'] === '') {
        $e['email'] = 'Email is required.';
    } elseif (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 150) {
        $e['email'] = 'Enter a valid email address (max 150 characters).';
    }

    if ($d['birthday'] === '') {
        $e['birthday'] = 'Birthday is required.';
    } elseif (!valid_date($d['birthday'])) {
        $e['birthday'] = 'Birthday must be a real date in YYYY-MM-DD format.';
    } elseif ($d['birthday'] > date('Y-m-d')) {
        $e['birthday'] = 'Birthday cannot be in the future.';
    }

    if (!in_array($d['sex'], ['Male', 'Female'], true)) {
        $e['sex'] = 'Sex is required and must be Male or Female.';
    }

    if ($d['student_number'] !== '') {
        if (!preg_match('/^[A-Za-z0-9]+$/', $d['student_number']) || strlen($d['student_number']) > 50) {
            $e['student_number'] = 'Student number must be alphanumeric, max 50 characters.';
        }
    }

    if (mb_strlen($d['program']) > 200) {
        $e['program'] = 'Program must be at most 200 characters.';
    }

    if ($d['enrolment_date'] === '') {
        $e['enrolment_date'] = 'Enrolment date is required.';
    } elseif (!valid_date($d['enrolment_date'])) {
        $e['enrolment_date'] = 'Enrolment date must be a real date in YYYY-MM-DD format.';
    }

    return $e;
}

/**
 * student_number is NOT NULL + UNIQUE and program is NOT NULL in the schema, but the lab
 * says both are optional. Normalise optional values so inserts always work.
 */
function normalise_for_db(array $d): array
{
    $d['middle_name'] = $d['middle_name'] !== '' ? $d['middle_name'] : null;
    if ($d['student_number'] === '') {
        $d['student_number'] = 'STU-' . strtoupper(bin2hex(random_bytes(4)));
    }
    return $d;
}

/** Which unique column caused a duplicate-key (1062) error? */
function duplicate_field(string $message): string
{
    return str_contains($message, 'student_number') ? 'student_number' : 'email';
}

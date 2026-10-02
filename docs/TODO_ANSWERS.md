# TODO answers (quick reference)

| TODO | Answer |
|---|---|
| 1 | `mysqli_report(MYSQLI_REPORT_ERROR \| MYSQLI_REPORT_STRICT);` |
| 2 | `$conn = new mysqli($host, $user, $pass, $db);` |
| 3 | `$conn->set_charset('utf8mb4');` |
| 4 | `SELECT id, first_name, last_name, email, enrolment_date FROM students ORDER BY enrolment_date DESC, id DESC` |
| 5 | `mysqli_fetch_assoc($result)` |
| 6 | `mysqli_free_result($result); mysqli_close($conn);` (free result first) |
| 7 | `SELECT ... FROM students WHERE id = ?` |
| 8 | `$res = $stmt->get_result(); $row = $res->fetch_assoc();` |
| 9 | `$row['id']`, `$row['first_name'] . ' ' . $row['last_name']`, ... |
| 10-12 | name: empty / strlen / preg_match; email: `!filter_var(..., FILTER_VALIDATE_EMAIL)`; birthday: `!preg_match('/^\d{4}-\d{2}-\d{2}$/')`; sex: `!in_array($sex, ['Male','Female'], true)` |
| 13 | `INSERT INTO students (id, ...) VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)` -> **9** placeholders |
| 14 | `'sssssssss'` (nine strings; the guide's "ten" counts the UUID, which is not bound) |
| 15 | collect + trim, run same validation |
| 16 | `UPDATE students SET ... WHERE id = ?`, bind `'ssssssssss'` (10 strings) |
| 17 | `$stmt->affected_rows`. 0 means "no row changed" (identical values) or "no row matched"; it is not an error |
| 18 | `SELECT first_name, last_name FROM students WHERE id = ?` |
| 19 | `$_SERVER['REQUEST_METHOD'] === 'POST'`; `DELETE FROM students WHERE id = ?`; check `$d->affected_rows === 1` |
| 20 | confirmation card + hidden `id` + Yes/Cancel |
| 21 | `mysql:host=127.0.0.1;dbname=ip10_lab;charset=utf8mb4` |
| 22 | `PDO::ATTR_AUTOCOMMIT => false` (then every write needs beginTransaction/commit) |
| PDO index | `$pdo->query($sql)->fetchAll()` |
| PDO execute | `execute([$id])`; create: 9 values in placeholder order; edit: 9 values then `$id` last |

## Errata in the lab handout (fixed in this code)
- PDO delete listing has `require_once 'config.php');` - stray `)` is a syntax error.
- TODO(14) says "ten strings" but only nine placeholders exist because `id` is `UUID()`.
- Student Number / Program are "optional" in validation but NOT NULL in the schema (and student_number is UNIQUE), so blanks are normalised in `validation.php`.

# Report draft - sections 7-9 (rewrite in your own words, add your own screenshots)

## 7. Comparison table
| Feature | mysqli | PDO | Notes |
|---|---|---|---|
| Error handling | `mysqli_report(MYSQLI_REPORT_ERROR \| MYSQLI_REPORT_STRICT)` throws `mysqli_sql_exception` | `ATTR_ERRMODE => ERRMODE_EXCEPTION` throws `PDOException` | Both can turn silent failures into exceptions. In PDO this is a per-connection option; in mysqli it is a global report mode that must be set before connecting. |
| Prepared-statement syntax | `prepare()`, `bind_param('sss', ...)`, `execute()` | `prepare()`, `execute([$a, $b])` | mysqli needs type codes and variable references; PDO takes a plain array, with named (`:id`) or positional (`?`) placeholders. |
| Default fetch mode | `fetch_assoc()` / `fetch_array()` chosen per call | `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC` set once | PDO removes repetition and supports many modes (`FETCH_OBJ`, `FETCH_CLASS`). |
| Transaction support | `begin_transaction()`, `commit()`, `rollback()`, `autocommit()` | `beginTransaction()`, `commit()`, `rollBack()`, `ATTR_AUTOCOMMIT` | Equivalent power. With autocommit off, forgetting `commit()` silently discards writes. |
| Database portability | MySQL/MariaDB only | ~12 drivers (MySQL, PostgreSQL, SQLite, SQL Server...) | Switching DB in PDO usually means changing the DSN, though SQL dialect differences (e.g. `UUID()`) remain. |
| Attribute configuration | Few options via `options()` / `set_charset()` | Rich `setAttribute()` / constructor options array | PDO's options array (emulated prepares, stringify fetches, error mode) centralises configuration. |

## 8. Challenges & Reflections (~250 words, DRAFT - personalise it with what actually happened to you)
The hardest part of the mysqli version was `bind_param()`. The type string has to match the number of values exactly, and I had to remember that a UUID is a string, so even the id in `WHERE id = ?` is bound as `'s'`; casting it to an integer would turn it into 0 and match nothing. Counting placeholders was also easy to get wrong: the insert has nine because `id` comes from `UUID()` in SQL. The PDO version was shorter, since `execute([...])` removes the binding step, but ordering matters: the array must match the placeholders, and in the update the id has to be last. Turning autocommit off in PDO also taught me that a write without `commit()` is lost.

I prefer PDO because the code is shorter, the attributes are set once in one place, and it is not tied to MySQL. mysqli is fine for a MySQL-only project, but I found PDO less error-prone.

On parameterized queries: values travel separately from the SQL text, so input such as `' OR 1=1 --` is treated as data and not executed, which prevents SQL injection. UUIDs avoid guessable sequential ids and can be created without the database, but they are bigger than integers, give no `insert_id`/`lastInsertId()`, and force me to confirm writes with `affected_rows`/`rowCount()`. ENUM columns only accept their listed values, so the form's sex dropdown and the server-side `in_array(..., true)` check must mirror the ENUM, or MySQL rejects the row. An update that reports 0 affected rows is not a failure: MySQL counts only rows whose values actually changed.

## 9. Conclusion (DRAFT)
This lab showed the same CRUD application with two database APIs and made the differences concrete. I practised prepared statements, server-side validation with per-field errors, safe output with `htmlspecialchars()`, confirm-before-delete using POST, and working with UUID keys. In a real project I would choose PDO for portability, cleaner syntax and centralised configuration, always with exceptions enabled and prepared statements for every query that touches user input.

<?php
/**
 * PDO connection. Every query in the app goes through prepared statements.
 */

function db_init(array $db): void
{
    $GLOBALS['__db_settings'] = $db;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $s = $GLOBALS['__db_settings'];
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $s['host'], $s['port'], $s['name']);

    try {
        $pdo = new PDO($dsn, $s['user'], $s['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(500);
        exit(APP_ENV === 'development'
            ? 'Database connection failed: ' . htmlspecialchars($e->getMessage())
            : 'Sorry, the shop is temporarily unavailable.');
    }

    return $pdo;
}

/** Run a prepared query and return the statement. */
function db_query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

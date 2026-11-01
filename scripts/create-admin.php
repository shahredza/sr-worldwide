<?php
/**
 * Create an admin account from the command line (there is no public admin sign-up page).
 *
 *   php scripts/create-admin.php <username>
 *
 * You will be asked for the password; it is never shown or stored in plain text.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$username = trim($argv[1] ?? '');
if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
    fwrite(STDERR, "Usage: php scripts/create-admin.php <username>\n(3-50 letters, digits, _ . -)\n");
    exit(1);
}

$config = require dirname(__DIR__) . '/includes/config.php';
$db = $config['db'];
$pdo = new PDO(
    sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['name']),
    $db['user'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

function ask_hidden(string $prompt): string
{
    echo $prompt;
    if (DIRECTORY_SEPARATOR === '/') {
        system('stty -echo');
        $value = trim((string) fgets(STDIN));
        system('stty echo');
        echo "\n";
    } else {
        $value = trim((string) fgets(STDIN));
    }
    return $value;
}

$password = ask_hidden('Password (min 8 characters): ');
if (strlen($password) < 8) {
    fwrite(STDERR, "Password too short.\n");
    exit(1);
}
if (ask_hidden('Repeat password: ') !== $password) {
    fwrite(STDERR, "Passwords do not match.\n");
    exit(1);
}

$stmt = $pdo->prepare(
    'INSERT INTO admins (username, password_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
);
$stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);

echo $stmt->rowCount() === 1 ? "Admin '$username' created.\n" : "Password for admin '$username' updated.\n";

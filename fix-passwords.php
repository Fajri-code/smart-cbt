<?php
/**
 * Standalone script to fix plain-text passwords in SQLite database
 * Run: php fix-passwords.php
 */

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    die("❌ Error: database/database.sqlite tidak ditemukan\n");
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 Scanning for plain-text passwords...\n";
    echo str_repeat("=", 70) . "\n";
    
    $stmt = $pdo->query('SELECT id, email, name, role, password FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $plainTextUsers = [];
    
    foreach ($users as $user) {
        $pwd = $user['password'];
        // Check if password is bcrypt hash (starts with $2y$, $2a$, or $2b$)
        if (!preg_match('/^\$2[aby]\$/', $pwd)) {
            $plainTextUsers[] = $user;
        }
    }
    
    if (empty($plainTextUsers)) {
        echo "✅ Semua password sudah aman (ter-hash)!\n";
        exit(0);
    }
    
    echo "⚠️  Found " . count($plainTextUsers) . " user(s) dengan plain-text password:\n";
    echo str_repeat("-", 70) . "\n";
    printf("%-4s | %-35s | %-10s | %-10s\n", "ID", "Email", "Role", "Name");
    echo str_repeat("-", 70) . "\n";
    
    foreach ($plainTextUsers as $user) {
        $email = substr($user['email'], 0, 33);
        printf("%-4d | %-35s | %-10s | %-10s\n", $user['id'], $email, $user['role'], substr($user['name'], 0, 9));
    }
    
    echo str_repeat("-", 70) . "\n\n";
    
    // Ask for confirmation
    echo "Tekan Y + Enter untuk hash semua password di atas, atau tekan yang lain untuk batal: ";
    $input = trim(fgets(STDIN));
    
    if (strtoupper($input) !== 'Y') {
        echo "Dibatalkan.\n";
        exit(0);
    }
    
    echo "\n🔐 Hashing passwords...\n";
    
    $fixed = 0;
    foreach ($plainTextUsers as $user) {
        $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        
        $updateStmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $updateStmt->execute([$hashedPassword, $user['id']]);
        
        echo "✅ Fixed: {$user['email']}\n";
        $fixed++;
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✅ Berhasil hash {$fixed} password(s)!\n";
    echo "🎉 User sekarang bisa login dengan password asli mereka!\n";
    
} catch (PDOException $e) {
    die("❌ Database error: " . $e->getMessage() . "\n");
}

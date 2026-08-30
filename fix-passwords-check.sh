#!/bin/bash

# Quick fix untuk plain-text passwords di SQLite database
# Script ini akan hash semua password yang belum ter-hash

SCRIPT_PATH=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_PATH"

# Check if database exists
if [ ! -f "database/database.sqlite" ]; then
    echo "❌ Error: database/database.sqlite tidak ditemukan"
    exit 1
fi

echo "🔍 Scanning plain-text passwords..."
echo ""

# Simple PHP script untuk fix passwords
php << 'EOF'
<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => 'database/database.sqlite',
]);
$capsule->setAsGlobal();

try {
    $users = $capsule->connection()->select('SELECT id, email, password, role FROM users');
    
    $plainTextUsers = [];
    foreach ($users as $user) {
        if (!str_starts_with($user->password, '$2y$') && 
            !str_starts_with($user->password, '$2a$') && 
            !str_starts_with($user->password, '$2b$')) {
            $plainTextUsers[] = $user;
        }
    }
    
    if (empty($plainTextUsers)) {
        echo "✅ No plain-text passwords found!\n";
        exit(0);
    }
    
    echo "⚠️  Found " . count($plainTextUsers) . " user(s) with plain-text passwords:\n";
    echo str_repeat("-", 60) . "\n";
    printf("%-5s | %-30s | %s\n", "ID", "Email", "Role");
    echo str_repeat("-", 60) . "\n";
    
    foreach ($plainTextUsers as $user) {
        printf("%-5d | %-30s | %s\n", $user->id, $user->email, $user->role);
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
EOF


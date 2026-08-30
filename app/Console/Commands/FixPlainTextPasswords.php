<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixPlainTextPasswords extends Command
{
    protected $signature = 'password:fix-plain-text {--dry-run : Show what would be fixed without actually changing}';
    protected $description = 'Hash all plain-text passwords in database to bcrypt';

    public function handle(): int
    {
        $this->info('Scanning for plain-text passwords...');
        
        $users = User::all();
        $plainTextUsers = [];

        foreach ($users as $user) {
            // Check if password looks like bcrypt hash (starts with $2y$)
            if (!str_starts_with($user->password, '$2y$') && !str_starts_with($user->password, '$2a$') && !str_starts_with($user->password, '$2b$')) {
                $plainTextUsers[] = $user;
            }
        }

        if (empty($plainTextUsers)) {
            $this->info('✅ No plain-text passwords found. All passwords are securely hashed!');
            return 0;
        }

        $this->warn('⚠️ Found ' . count($plainTextUsers) . ' user(s) with plain-text passwords:');
        $this->line('');

        // Display table
        $rows = [];
        foreach ($plainTextUsers as $user) {
            $rows[] = [
                $user->id,
                $user->email,
                $user->name,
                $user->role,
                mb_substr($user->password, 0, 30) . (strlen($user->password) > 30 ? '...' : ''),
            ];
        }

        $this->table(['ID', 'Email', 'Name', 'Role', 'Password (truncated)'], $rows);

        if ($this->option('dry-run')) {
            $this->info('DRY RUN: No changes made. Run without --dry-run to fix.');
            return 0;
        }

        if (!$this->confirm('Do you want to hash these passwords?')) {
            $this->info('Aborted.');
            return 0;
        }

        $fixed = 0;
        foreach ($plainTextUsers as $user) {
            $user->update(['password' => Hash::make($user->password)]);
            $this->line("✅ Fixed: {$user->email}");
            $fixed++;
        }

        $this->info('');
        $this->info("✅ Successfully hashed {$fixed} password(s)!");
        return 0;
    }
}

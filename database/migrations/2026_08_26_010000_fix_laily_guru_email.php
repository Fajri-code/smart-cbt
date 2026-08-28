<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $correctEmail = 'lailysalmahanum@smart.sch.id';
        $legacyEmail = 'lailysalmahannum@smart.sch.id';

        if (! DB::table('users')->where('email', $correctEmail)->exists()) {
            DB::table('users')->where('email', $legacyEmail)->update(['email' => $correctEmail]);
        }
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'lailysalmahanum@smart.sch.id')
            ->update(['email' => 'lailysalmahannum@smart.sch.id']);
    }
};
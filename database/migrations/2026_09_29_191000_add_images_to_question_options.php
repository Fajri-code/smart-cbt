<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('opsi_a_image')->nullable()->after('opsi_a');
            $table->string('opsi_b_image')->nullable()->after('opsi_b');
            $table->string('opsi_c_image')->nullable()->after('opsi_c');
            $table->string('opsi_d_image')->nullable()->after('opsi_d');
            $table->string('opsi_e_image')->nullable()->after('opsi_e');
        });

        Schema::table('bank_questions', function (Blueprint $table) {
            $table->string('opsi_a_image')->nullable()->after('opsi_a');
            $table->string('opsi_b_image')->nullable()->after('opsi_b');
            $table->string('opsi_c_image')->nullable()->after('opsi_c');
            $table->string('opsi_d_image')->nullable()->after('opsi_d');
            $table->string('opsi_e_image')->nullable()->after('opsi_e');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['opsi_a_image', 'opsi_b_image', 'opsi_c_image', 'opsi_d_image', 'opsi_e_image']);
        });

        Schema::table('bank_questions', function (Blueprint $table) {
            $table->dropColumn(['opsi_a_image', 'opsi_b_image', 'opsi_c_image', 'opsi_d_image', 'opsi_e_image']);
        });
    }
};

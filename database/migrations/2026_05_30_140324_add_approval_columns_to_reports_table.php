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
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('status', ['Submitted', 'Reviewed', 'Approved', 'Rejected'])->default('Submitted')->after('executive_summary');
            $table->text('rejection_note')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejection_note']);
        });
    }
};

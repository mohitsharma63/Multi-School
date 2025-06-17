<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToRemainingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'payments',
            'staff_records',
            'books',
            'book_requests',
            'exams',
            'exam_records',
            'time_tables',
            'time_slots',
            'my_classes',
            'sections',
            'subjects',
            'marks',
            'grades',
            'receipts',
            'promotions',
            'settings'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                    $table->index('branch_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'payments',
            'staff_records',
            'books',
            'book_requests',
            'exams',
            'exam_records',
            'time_tables',
            'time_slots',
            'my_classes',
            'sections',
            'subjects',
            'marks',
            'grades',
            'receipts',
            'promotions',
            'settings'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                });
            }
        }
    }
}

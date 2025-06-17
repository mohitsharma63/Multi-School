<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToExistingTables extends Migration
{
    public function up()
    {
        // Add branch_id to my_classes table
        Schema::table('my_classes', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('class_type_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add branch_id to subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('slug');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add branch_id to sections table
        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('teacher_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add branch_id to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('year');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add branch_id to exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('year');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add branch_id to time_table_records table
        Schema::table('time_table_records', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('year');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add branch_id to books table
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('isbn');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });

        // Add foreign key to student_records table
        Schema::table('student_records', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('my_classes', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('time_table_records', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('student_records', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
    }
}

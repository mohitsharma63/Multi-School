<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchoolBranchToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('school_id')->nullable()->after('user_type');
            $table->unsignedInteger('branch_id')->nullable()->after('school_id');
            $table->unsignedInteger('role_id')->nullable()->after('branch_id');

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['school_id', 'branch_id', 'role_id']);
        });
    }
}

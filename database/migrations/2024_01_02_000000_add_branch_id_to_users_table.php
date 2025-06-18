<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AddBranchIdToUsersTable extends Migration
{
    public function up()
    {
        // First ensure the branches table exists
        if (!Schema::hasTable('branches')) {
            Log::error('Branches table does not exist. Please create it first.');
            throw new \RuntimeException('Branches table does not exist. Please create it first.');
        }

        // Add branch_id column to users table
        Schema::table('users', function (Blueprint $table) {
            // Check if column already exists to avoid errors
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->after('id')->nullable();

                // Add index for better performance
                $table->index('branch_id');
            }
        });

        // Add foreign key constraint in a separate operation
        Schema::table('users', function (Blueprint $table) {
            // Verify the branches table and id column exist
            if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'id')) {
                // Check if foreign key doesn't already exist
                $connection = Schema::getConnection();
                $foreignKeys = $connection->getDoctrineSchemaManager()->listTableForeignKeys('users');

                $foreignKeyExists = false;
                foreach ($foreignKeys as $key) {
                    if ($key->getForeignTableName() === 'branches' &&
                        in_array('branch_id', $key->getLocalColumns())) {
                        $foreignKeyExists = true;
                        break;
                    }
                }

                if (!$foreignKeyExists) {
                    $table->foreign('branch_id')
                          ->references('id')
                          ->on('branches')
                          ->onDelete('set null')
                          ->onUpdate('cascade');
                }
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['branch_id']);

            // Then drop the column
            $table->dropColumn('branch_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddSchoolIdToBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if schools table exists first
        if (!Schema::hasTable('schools')) {
            Log::error('Schools table does not exist. Please create it first.');
            throw new \RuntimeException('Schools table does not exist. Please create it first.');
        }

        // Start transaction for safety
        DB::beginTransaction();

        try {
            // Add school_id column if it doesn't exist
            if (!Schema::hasColumn('branches', 'school_id')) {
                Schema::table('branches', function (Blueprint $table) {
                    $table->unsignedBigInteger('school_id')
                          ->nullable()
                          ->after('id')
                          ->comment('Reference to schools table');
                });
                Log::info('Added school_id column to branches table');
            }

            // Check if foreign key already exists
            $connection = Schema::getConnection();
            $foreignKeys = $connection->getDoctrineSchemaManager()->listTableForeignKeys('branches');

            $foreignKeyExists = false;
            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey->getForeignTableName() === 'schools' &&
                    in_array('school_id', $foreignKey->getLocalColumns())) {
                    $foreignKeyExists = true;
                    break;
                }
            }

            // Add foreign key constraint if it doesn't exist
            if (!$foreignKeyExists) {
                Schema::table('branches', function (Blueprint $table) {
                    $table->foreign('school_id')
                          ->references('id')
                          ->on('schools')
                          ->onDelete('cascade')
                          ->onUpdate('cascade');
                });
                Log::info('Added foreign key constraint from branches.school_id to schools.id');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();

        try {
            // Drop foreign key first if it exists
            $connection = Schema::getConnection();
            $foreignKeys = $connection->getDoctrineSchemaManager()->listTableForeignKeys('branches');

            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey->getForeignTableName() === 'schools' &&
                    in_array('school_id', $foreignKey->getLocalColumns())) {
                    Schema::table('branches', function (Blueprint $table) use ($foreignKey) {
                        $table->dropForeign([$foreignKey->getName()]);
                    });
                    Log::info('Dropped foreign key constraint from branches.school_id to schools.id');
                    break;
                }
            }

            // Drop column if it exists
            if (Schema::hasColumn('branches', 'school_id')) {
                Schema::table('branches', function (Blueprint $table) {
                    $table->dropColumn('school_id');
                });
                Log::info('Dropped school_id column from branches table');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Migration rollback failed: ' . $e->getMessage());
            throw $e;
        }
    }
}

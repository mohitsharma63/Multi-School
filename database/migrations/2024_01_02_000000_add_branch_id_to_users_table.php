<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AddBranchIdToUsersTable extends Migration
{
    public function up()
    {
        // First ensure the schools table exists and has the correct structure
        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $table->bigIncrements('id');
                // Add other necessary columns for schools table
                $table->timestamps();
            });
        }

        // Then modify the branches table
        Schema::table('branches', function (Blueprint $table) {
            // Check if column already exists to avoid errors
            if (!Schema::hasColumn('branches', 'school_id')) {
                $table->unsignedBigInteger('school_id')->after('id')->nullable();

                // Add index for better performance
                $table->index('school_id');
            }
        });

        // Add foreign key constraint in a separate operation
        Schema::table('branches', function (Blueprint $table) {
            // Verify the schools table and id column exist
            if (Schema::hasTable('schools') && Schema::hasColumn('schools', 'id')) {
                // Check if foreign key doesn't already exist
                $connection = Schema::getConnection();
                $foreignKeys = $connection->getDoctrineSchemaManager()->listTableForeignKeys('branches');

                $foreignKeyExists = false;
                foreach ($foreignKeys as $key) {
                    if ($key->getForeignTableName() === 'schools' &&
                        in_array('school_id', $key->getLocalColumns())) {
                        $foreignKeyExists = true;
                        break;
                    }
                }

                if (!$foreignKeyExists) {
                    $table->foreign('school_id')
                          ->references('id')
                          ->on('schools')
                          ->onDelete('cascade')
                          ->onUpdate('cascade');
                }
            }
        });

        // For existing data, you might want to set a default school_id
        // DB::table('branches')->whereNull('school_id')->update(['school_id' => 1]);
    }

    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['school_id']);

            // Then drop the column
            $table->dropColumn('school_id');
        });
    }
}

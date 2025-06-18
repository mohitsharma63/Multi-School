<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolIdToMyClassesTable extends Migration
{
    public function up()
    {
        Schema::table('my_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('my_classes', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('class_type_id');
            }
        });

        // Add foreign key only if schools table exists with proper structure
        if (Schema::hasTable('schools')) {
            Schema::table('my_classes', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $schoolsTable = $sm->listTableDetails('schools');
                $myClassesTable = $sm->listTableDetails('my_classes');

                // Check if schools table has proper id column
                $hasProperId = $schoolsTable->hasColumn('id') &&
                              $schoolsTable->getColumn('id')->getType()->getName() === 'bigint' &&
                              $schoolsTable->getColumn('id')->getUnsigned();

                // Check if my_classes has school_id column
                $hasSchoolId = $myClassesTable->hasColumn('school_id') &&
                               $myClassesTable->getColumn('school_id')->getType()->getName() === 'bigint' &&
                               $myClassesTable->getColumn('school_id')->getUnsigned();

                // Check if foreign key doesn't exist
                $foreignKeyExists = false;
                foreach ($sm->listTableForeignKeys('my_classes') as $foreignKey) {
                    if (in_array('school_id', $foreignKey->getColumns())) {
                        $foreignKeyExists = true;
                        break;
                    }
                }

                if ($hasProperId && $hasSchoolId && !$foreignKeyExists) {
                    $table->foreign('school_id')
                          ->references('id')
                          ->on('schools')
                          ->onDelete('cascade');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('my_classes', function (Blueprint $table) {
            // Drop foreign key if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys('my_classes');

            foreach ($foreignKeys as $foreignKey) {
                if (in_array('school_id', $foreignKey->getColumns())) {
                    $table->dropForeign(['school_id']);
                    break;
                }
            }

            // Drop column if it exists
            if (Schema::hasColumn('my_classes', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });
    }
}

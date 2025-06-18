<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyClassesTable extends Migration
{
    public function up()
    {
        Schema::create('my_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedInteger('class_type_id')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->timestamps();

            $table->index('school_id');
        });

        // Add unique constraint
        Schema::table('my_classes', function (Blueprint $table) {
            $table->unique(['class_type_id', 'name']);
        });

        // Add foreign key constraint only if schools table exists with matching column
        if (Schema::hasTable('schools')) {
            Schema::table('my_classes', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $schoolsTable = $sm->listTableDetails('schools');

                // Check if schools table has 'id' column and it's unsigned big integer
                if ($schoolsTable->hasColumn('id') &&
                    $schoolsTable->getColumn('id')->getType()->getName() === 'bigint' &&
                    $schoolsTable->getColumn('id')->getUnsigned()) {

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
            $table->dropUnique(['class_type_id', 'name']);

            // Drop foreign key if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys('my_classes');

            foreach ($foreignKeys as $foreignKey) {
                if (in_array('school_id', $foreignKey->getColumns())) {
                    $table->dropForeign(['school_id']);
                    break;
                }
            }
        });

        Schema::dropIfExists('my_classes');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateSchoolsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if (!Schema::hasTable('schools')) {
            try {
                Schema::create('schools', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code')->unique();
                    $table->text('address');
                    $table->string('phone')->nullable();
                    $table->string('email')->nullable();
                    $table->string('system_title')->nullable();
                    $table->string('current_session')->nullable();
                    $table->date('term_begins')->nullable();
                    $table->date('term_ends')->nullable();
                    $table->string('logo')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

                Log::info('Schools table created successfully.');
            } catch (\Exception $e) {
                Log::error('Failed to create schools table: ' . $e->getMessage());
                throw $e;
            }
        } else {
            // Table exists, let's check and add any missing columns
            try {
                Schema::table('schools', function (Blueprint $table) {
                    $columnsToCheck = [
                        'name' => 'string',
                        'code' => 'string',
                        'address' => 'text',
                        'phone' => 'string|nullable',
                        'email' => 'string|nullable',
                        'system_title' => 'string|nullable',
                        'current_session' => 'string|nullable',
                        'term_begins' => 'date|nullable',
                        'term_ends' => 'date|nullable',
                        'logo' => 'string|nullable',
                        'is_active' => 'boolean'
                    ];

                    foreach ($columnsToCheck as $column => $type) {
                        if (!Schema::hasColumn('schools', $column)) {
                            $typeParts = explode('|', $type);
                            $columnType = $typeParts[0];
                            $isNullable = in_array('nullable', $typeParts);

                            $columnDefinition = $table->{$columnType}($column);

                            if ($isNullable) {
                                $columnDefinition->nullable();
                            }

                            if ($column === 'code') {
                                $columnDefinition->unique();
                            }

                            if ($column === 'is_active') {
                                $columnDefinition->default(true);
                            }
                        }
                    }

                    // Add timestamps if they don't exist
                    if (!Schema::hasColumn('schools', 'created_at') ||
                        !Schema::hasColumn('schools', 'updated_at')) {
                        $table->timestamps();
                    }
                });

                Log::info('Existing schools table updated with missing columns.');
            } catch (\Exception $e) {
                Log::error('Failed to update schools table: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    public function down()
    {
        // Only drop the table if it exists
        if (Schema::hasTable('schools')) {
            try {
                // First drop foreign key constraints from other tables
                $this->dropForeignKeysReferencingSchools();

                // Then drop the table
                Schema::dropIfExists('schools');
                Log::info('Schools table dropped successfully.');
            } catch (\Exception $e) {
                Log::error('Failed to drop schools table: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * Drop all foreign keys that reference the schools table
     */
    protected function dropForeignKeysReferencingSchools()
    {
        $connection = Schema::getConnection();
        $tables = $connection->getDoctrineSchemaManager()->listTableNames();

        foreach ($tables as $tableName) {
            $foreignKeys = $connection->getDoctrineSchemaManager()->listTableForeignKeys($tableName);

            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey->getForeignTableName() === 'schools') {
                    $columns = $foreignKey->getLocalColumns();
                    $constraintName = $foreignKey->getName();

                    Schema::table($tableName, function (Blueprint $table) use ($constraintName) {
                        $table->dropForeign($constraintName);
                    });
                }
            }
        }
    }
}

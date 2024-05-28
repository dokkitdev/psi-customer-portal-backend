<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SimproJobsTableAddSuccessStatus extends Migration
{
    public function up()
    {
        $this->changeEnumValues('simpro_jobs', 'handle_status', ['new', 'error', 'success']);
    }

    public function down()
    {
        DB::table('simpro_jobs')->where('handle_status', 'success')->delete();

        $this->changeEnumValues('simpro_jobs', 'handle_status', ['new', 'error']);
    }

    protected function changeEnumValues(string $table, string $field, array $values): void
    {
        DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$table}_{$field}_check");

        $result = join(', ', array_map(function ($value) {
            return sprintf("'%s'::character varying", $value);
        }, $values));

        DB::statement("ALTER TABLE {$table} add CONSTRAINT {$table}_{$field}_check CHECK ({$field}::text = ANY (ARRAY[$result]::text[]))");
    }
}

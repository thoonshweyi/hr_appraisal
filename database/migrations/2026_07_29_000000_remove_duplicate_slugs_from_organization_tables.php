<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateSlugsFromOrganizationTables extends Migration
{
    /**
     * Remove rows with duplicate slugs while preserving the row with the
     * smallest ID. References to duplicate rows are moved to the kept row.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $this->removeDuplicates('divisions', [
                'agile_departments' => 'division_id',
                'sub_departments' => 'division_id',
                'sections' => 'division_id',
                'positions' => 'division_id',
                'sub_sections' => 'division_id',
                'employees' => 'division_id',
            ]);

            $this->removeDuplicates('agile_departments');

            $this->removeDuplicates('sub_departments', [
                'sections' => 'sub_department_id',
                'positions' => 'sub_department_id',
                'sub_sections' => 'sub_department_id',
                'employees' => 'sub_department_id',
            ]);

            $this->removeDuplicates('sections', [
                'positions' => 'section_id',
                'employees' => 'section_id',
            ]);

            $this->removeDuplicates('positions', [
                'employees' => 'position_id',
            ]);

            $this->removeDuplicates('sub_sections', [
                'employees' => 'sub_section_id',
            ]);
        });
    }

    /**
     * Deleted duplicate rows cannot be restored reliably.
     *
     * @return void
     */
    public function down()
    {
        // This data-cleanup migration is intentionally irreversible.
    }

    /**
     * @param  string  $table
     * @param  array<string, string>  $references
     * @return void
     */
    private function removeDuplicates($table, array $references = [])
    {
        $duplicates = DB::table($table . ' as duplicate')
            ->join($table . ' as keeper', function ($join) {
                $join->on('duplicate.slug', '=', 'keeper.slug')
                    ->whereColumn('duplicate.id', '>', 'keeper.id');
            })
            ->select('duplicate.id')
            ->selectRaw('MIN(keeper.id) as keeper_id')
            ->groupBy('duplicate.id')
            ->orderBy('duplicate.id')
            ->get();
        // dd($duplicates);

        foreach ($duplicates as $duplicate) {
            foreach ($references as $referenceTable => $foreignKey) {
                DB::table($referenceTable)
                    ->where($foreignKey, $duplicate->id)
                    ->update([$foreignKey => $duplicate->keeper_id]);
            }

            DB::table($table)
                ->where('id', $duplicate->id)
                ->delete();
        }
    }
}

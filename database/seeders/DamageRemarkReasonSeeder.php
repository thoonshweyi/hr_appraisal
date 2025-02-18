<?php

namespace Database\Seeders;

use App\Models\DamageRemarkReason;
use Illuminate\Database\Seeder;

class DamageRemarkReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DamageRemarkReason::factory()->create(['name'=>'Custom']);
        DamageRemarkReason::factory()->create(['name'=>'Shortage/Surplus ']);
        DamageRemarkReason::factory()->create(['name'=>'Overdue']);
    }
}

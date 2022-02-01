<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $s = new Status();
        $s->id = 30;
        $s->index = 'absent';
        $s->name = 'отсутсвует';
        $s->ID_on_bitrix = 'close';
        $s->color = '#EB5757';
        $s->status_type = 'finished';
        $s->save();

        $s = new Status();
        $s->id = 31;
        $s->index = 'absent';
        $s->name = 'отсутсвует';
        $s->ID_on_bitrix = 'delete';
        $s->color = '#EB5757';
        $s->status_type = 'finished';
        $s->save();

    }
}

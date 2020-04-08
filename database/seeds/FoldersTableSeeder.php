<?php

use Illuminate\Database\Seeder;
use \Carbon\Carbon;

class FoldersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('folders')->insert([
            'id'=>Ramsey\Uuid\Uuid::uuid4(),
            'text' => 'C:',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}

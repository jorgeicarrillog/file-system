<?php

use Illuminate\Database\Seeder;
use \Carbon\Carbon;

class FileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('files')->insert([
            'id'=>Ramsey\Uuid\Uuid::uuid4(),
            'text' => 'por-defecto.txt',
            'parent' => \App\Folder::first()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}

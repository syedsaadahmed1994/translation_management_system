<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    public function run(){

        /* only inserts if count is 0 */
        if (DB::table('languages')->count() === 0) {
            DB::table('languages')->insert([
                ['code' => 'en' , 'name' => 'English'],
                ['code' => 'fr', 'name' => 'French']
            ]);
        }

        $languageIds = DB::table('languages')->pluck('id')->toArray();

        $values = [];
        
        for($i = 0;$i<=100000; $i++){
            $values[] = [
                'key' => "key_$i",
                'content' => "Content for key_$i",
                'language_id' => $languageIds[array_rand($languageIds)],
                'created_at' => now(),
                'updated_at' => now()
            ];

            if($i%10000 === 0){
                DB::table('translations')->insert($values);
                $values = [];
            }
        }

        if(!empty($values)){
            DB::table('translations')->insert($values);
        }
    }
}
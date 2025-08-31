<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Party;

class PartySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['type'=>'pengirim','code'=>'SUPP001','name'=>'TAGA CO.,LTD.','address'=>'2-6-4 , BESSHO, MINAMI-KU, SAITAMA-CITY, SAITAMA 336-0021 JAPAN','country'=>'JP'],
            
            ['type'=>'penjual','code'=>'SELL001','name'=>'TAGA PRECISION SINGAPORE PTE LTD','address'=>'10 ANSON ROAD,#16-01 INTERNATIONAL PLAZA SINGAPORE 079903','country'=>'SG'],
                    ];
        foreach ($rows as $r) Party::updateOrCreate(['code'=>$r['code']], $r);
    }
}

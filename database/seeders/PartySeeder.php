<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Party;

class PartySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['type'=>'pengirim','code'=>'SUPP001','name'=>'ABC Co., Ltd.','address'=>'1-2-3 Chiyoda, Tokyo','country'=>'JP'],
            ['type'=>'pengirim','code'=>'SUPP002','name'=>'XYZ Manufacturing Inc.','address'=>'88 Harbor Rd, Shenzhen','country'=>'CN'],
            ['type'=>'pengirim','code'=>'SUPP003','name'=>'Nippon Parts Co.','address'=>'Osaka Bay','country'=>'JP'],

            ['type'=>'penjual','code'=>'SELL001','name'=>'Global Trade Pte. Ltd.','address'=>'12 Marina Blvd','country'=>'SG'],
            ['type'=>'penjual','code'=>'SELL002','name'=>'Pacific Traders Co.','address'=>'Jl. Sudirman No. 1','country'=>'ID'],
            ['type'=>'penjual','code'=>'SELL003','name'=>'EuroParts GmbH','address'=>'Hafenstrasse 5','country'=>'DE'],
        ];
        foreach ($rows as $r) Party::updateOrCreate(['code'=>$r['code']], $r);
    }
}

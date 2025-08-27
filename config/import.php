<?php

return [
    // contoh subset, silakan tambah sesuai kebutuhan
    'kantor_pabean' => [
        '020100' => '020100 - KPPBC Tipe Madya Pabean Tanjung Priok',
        '040300' => '040300 - KPPBC Soekarno-Hatta',
        '010700' => '010700 - KPPBC Tanjung Perak',
    ],
    'jenis_pib' => [
        '1' => 'PIB Biasa',
        '2' => 'PIB Berkala',
    ],
    'jenis_impor' => [
        '1' => 'Untuk Dipakai',
        '2'     => 'Sementara',
    ],
    'cara_pembayaran' => [
        '1'          => 'Biasa',
        '2'          => 'Berkala',
    ],
    'status_importir' => [
        'AEO' => 'AEO (Authorized Economic Operator)',
        'MITA' => 'MITA Prioritas',
        'UMUM' => 'Umum',
    ],

    // Contoh master nama (pakai Select2). Nanti bisa diganti ke DB/remote.
    'master_pengirim' => [
        'SUPP001' => 'ABC Co., Ltd.',
        'SUPP002' => 'XYZ Manufacturing Inc.',
        'SUPP003' => 'Nippon Parts Co.',
    ],
    'master_penjual' => [
        'SELL001' => 'Global Trade Pte. Ltd.',
        'SELL002' => 'Pacific Traders Co.',
        'SELL003' => 'EuroParts GmbH',
    ],

    // (opsional) master negara ISO 2 → label
    'negara' => [
        'ID' => 'Indonesia',
        'JP' => 'Japan',
        'CN' => 'China',
        'US' => 'United States',
        'SG' => 'Singapore',
        'DE' => 'Germany',
        // tambahkan sesuai kebutuhan
    ],
];

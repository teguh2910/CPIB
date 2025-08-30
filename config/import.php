<?php

return [
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
        '2' => 'Sementara',
    ],
    'cara_pembayaran' => [
        '1' => 'Biasa',
        '2' => 'Berkala',
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
    'jenis_dokumen' => [
        'INV' => 'Invoice',
        'PL' => 'Packing List',
        'BLAWB' => 'Bill of Lading / Air Waybill',
        'LC' => 'Letter of Credit',
        'COO' => 'Certificate of Origin',
        'INS' => 'Insurance',
        'OTH' => 'Dokumen Lainnya',
    ],
    'cara_pengangkutan' => [
        '1' => 'Laut (Sea)',
        '2' => 'Udara (Air)',
    ],
    'negara' => [
        'ID' => 'Indonesia',
        'SG' => 'Singapore',
        'JP' => 'Japan',
        'CN' => 'China',
        'US' => 'United States',
        'MY' => 'Malaysia',
    ],

    // pelabuhan (UN/Locode contoh)
    'pelabuhan' => [
        'IDJKT' => 'IDJKT - Tanjung Priok (Jakarta)',
        'IDSUB' => 'IDSUB - Tanjung Perak (Surabaya)',
        'IDCGK' => 'IDCGK - Soekarno-Hatta (Jakarta Air)',
        'SGSIN' => 'SGSIN - Singapore',
        'JPTYO' => 'JPTYO - Tokyo',
        'CNSZX' => 'CNSZX - Shenzhen',
    ],

    // Tempat Penimbunan Sementara (TPS) / Gudang
    'tps' => [
        'TPS01' => 'TPS01 - TPS Koja',
        'TPS02' => 'TPS02 - TPS Tanjung Priok',
        'BOND01' => 'BOND01 - Gudang Berikat A',
    ],
    'jenis_kemasan' => [
        'CT' => 'Carton',
        'BX' => 'Box',
        'PK' => 'Pack',
        'BG' => 'Bag',
        'CS' => 'Case',
        'DR' => 'Drum',
        'PL' => 'Pallet',
        'RL' => 'Roll',
        'OTH' => 'Lainnya',
    ],

    // Ukuran peti kemas
    'ukuran_petikemas' => [
        '20' => '20 Feet',
        '40' => '40 Feet',
        '45' => '45 Feet',
        '20H' => '20 High Cube',
        '40H' => '40 High Cube',
    ],

    // Jenis muatan peti kemas
    'jenis_muatan_petikemas' => [
        'FCL' => 'FCL (Full Container Load)',
        'LCL' => 'LCL (Less than Container Load)',
        'MTY' => 'Empty',
        'BBK' => 'Breakbulk in Container',
    ],

    // Tipe peti kemas
    'tipe_petikemas' => [
        'GP' => 'General Purpose (Dry)',
        'HC' => 'High Cube',
        'RF' => 'Reefer (Refrigerated)',
        'OT' => 'Open Top',
        'FR' => 'Flat Rack',
        'TK' => 'Tank',
    ],
    'jenis_valuta' => [
        'IDR' => 'IDR - Rupiah',
        'USD' => 'USD - US Dollar',
        'EUR' => 'EUR - Euro',
        'JPY' => 'JPY - Yen',
        'SGD' => 'SGD - Singapore Dollar',
    ],
    'jenis_transaksi' => [
        'JUAL-BELI' => 'Jual Beli',
        'KONSINYASI' => 'Konsinyasi',
        'HIBAH' => 'Hibah/Gratis',
        'PINJAM' => 'Pinjam Pakai',
        'LAIN' => 'Lainnya',
    ],
    'incoterm' => [
        'EXW' => 'EXW - Ex Works',
        'FCA' => 'FCA - Free Carrier',
        'FOB' => 'FOB - Free On Board',
        'CFR' => 'CFR - Cost and Freight',
        'CIF' => 'CIF - Cost, Insurance & Freight',
        'DAP' => 'DAP - Delivered At Place',
        'DDP' => 'DDP - Delivered Duty Paid',
    ],
    'jenis_asuransi' => [
        'LN' => 'LUAR NEGERI',
        'DN' => 'DALAM NEGERI',
    ],
    'kondisi_barang' => [
        'BAIK/BARU' => 'BAIK/BARU',
        'BEKAS' => 'BEKAS',
    ],
    // satuan barang contoh
    'satuan_barang' => [
        'PCS' => 'PCS - Pieces',
        'SET' => 'SET - Set',
        'KG' => 'KG - Kilogram',
        'M' => 'M - Meter',
        'L' => 'L - Liter',
        'BOX' => 'BOX - Box',
    ],
];

<?php

namespace App\Imports;

use App\Models\Party;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PartiesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $type = $row['type'] ?? $row['jenis'] ?? null;
        $code = $row['code'] ?? $row['kode'] ?? null;

        // Skip if code already exists
        if ($code && Party::where('code', $code)->exists()) {
            return null;
        }

        return new Party([
            'type' => $type,
            'code' => $code,
            'name' => $row['name'] ?? $row['nama'] ?? null,
            'address' => $row['address'] ?? $row['alamat'] ?? null,
            'country' => $row['country'] ?? $row['negara'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:pengirim,penjual'],
            'code' => ['required', 'string', 'max:50', 'alpha_dash'],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'size:2'],
        ];
    }
}

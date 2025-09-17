<?php

namespace App\Http\Controllers;

use App\Imports\PartiesImport;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $type = $request->get('type', '');
        $query = Party::query()
            ->when($type, fn ($qr) => $qr->where('type', $type))
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                        ->orWhere('code', 'like', "%$q%")
                        ->orWhere('address', 'like', "%$q%");
                });
            })
            ->orderBy('type')->orderBy('name');

        $parties = $query->paginate(12)->appends($request->query());
        $countries = config('import.negara');

        return view('parties.index', compact('parties', 'q', 'type', 'countries'));
    }

    public function create()
    {
        $countries = config('import.negara');

        return view('parties.create', compact('countries'));
    }

    public function show(Party $party)
    {
        $countries = config('import.negara');

        return view('parties.show', compact('party', 'countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:pengirim,penjual'],
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('parties', 'code')],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'size:2'],
        ]);

        Party::create($data);

        return redirect()->route('parties.index')->with('success', 'Master berhasil dibuat.');
    }

    public function edit(Party $party)
    {
        $countries = config('import.negara');

        return view('parties.edit', compact('party', 'countries'));
    }

    public function update(Request $request, Party $party)
    {
        $data = $request->validate([
            'type' => ['required', 'in:pengirim,penjual'],
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('parties', 'code')->ignore($party->id)],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'size:2'],
        ]);

        $party->update($data);

        return redirect()->route('parties.index')->with('success', 'Master diperbarui.');
    }

    public function destroy(Party $party)
    {
        $party->delete();

        return redirect()->route('parties.index')->with('success', 'Master dihapus.');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'], // 10MB max
        ]);

        try {
            Excel::import(new PartiesImport, $request->file('excel_file'));

            return redirect()->route('parties.index')->with('success', 'Data berhasil diimpor dari Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris '.$failure->row().': '.implode(', ', $failure->errors());
            }

            return redirect()->route('parties.index')->withErrors($errors);
        } catch (\Exception $e) {
            return redirect()->route('parties.index')->withErrors(['Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['type', 'code', 'name', 'address', 'country'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1).'1', $header);
        }

        // Add sample data
        $sampleData = [
            ['pengirim', 'SUP001', 'PT Supplier Indonesia', 'Jl. Sudirman No. 123, Jakarta', 'ID'],
            ['penjual', 'SEL001', 'CV Seller Nusantara', 'Jl. Malioboro No. 45, Yogyakarta', 'ID'],
            ['pengirim', 'EXP001', 'ABC Export Company', '123 Main St, New York', 'US'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            foreach ($data as $col => $value) {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1).$row, $value);
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'template_parties_'.date('Y-m-d').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}

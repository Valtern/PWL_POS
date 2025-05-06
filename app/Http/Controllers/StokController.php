<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class StokController extends Controller
{
    public function index()
    {
        $activeMenu = 'stok';
        $breadcrumb = (object) [
            'title' => 'Data Stok',
            'list'  => ['Home', 'Stok']
        ];
        $barangs = BarangModel::select('barang_id', 'barang_nama')->get();
        $users = UserModel::select('user_id', 'nama')->get();

        return view('stok.index', compact('activeMenu', 'breadcrumb', 'barangs', 'users'));
    }

    public function list(Request $request)
    {
        $query = StokModel::with('barang', 'user');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('barang_nama', fn($s) => $s->barang->barang_nama)
            ->addColumn('user_nama', fn($s) => $s->user->nama)
            ->addColumn('action', function($s) {
                $btn = '';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $s->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $s->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $s->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Delete</button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) ['title' => 'Tambah Stok', 'list' => ['Home', 'Stok', 'Tambah']];
        $page = (object) ['title' => 'Tambah Stok Baru'];
        $activeMenu = 'stok';
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.create', compact('breadcrumb', 'page', 'activeMenu', 'barangs', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer'
        ]);

        StokModel::create($request->all());
        return redirect('/stok')->with('success', 'Data stok berhasil ditambahkan!');
    }

    public function show($id)
    {
        $stok = StokModel::with('barang', 'user')->find($id);
        $breadcrumb = (object) ['title' => 'Detail Stok', 'list' => ['Home', 'Stok', 'Detail']];
        $page = (object) ['title' => 'Detail Data Stok'];
        $activeMenu = 'stok';

        return view('stok.show', compact('breadcrumb', 'page', 'activeMenu', 'stok'));
    }

    public function edit($id)
    {
        $stok = StokModel::find($id);
        $breadcrumb = (object) ['title' => 'Edit Stok', 'list' => ['Home', 'Stok', 'Edit']];
        $page = (object) ['title' => 'Edit Data Stok'];
        $activeMenu = 'stok';
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.edit', compact('breadcrumb', 'page', 'activeMenu', 'stok', 'barangs', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer'
        ]);

        $stok = StokModel::find($id);
        $stok->update($request->all());

        return redirect('/stok')->with('success', 'Data stok berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $stok = StokModel::find($id);
        $stok->delete();

        return redirect('/stok')->with('success', 'Data stok berhasil dihapus!');
    }

    // --- AJAX ---

    public function create_ajax()
    {
        $barangs = BarangModel::all();
        $users = UserModel::all();
        return view('stok.create_ajax', compact('barangs', 'users'));
    }

    public function store_ajax(Request $request)
    {
        $rules = [
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors()]);
        }

        StokModel::create($request->all());
        return response()->json(['status' => true, 'message' => 'Data stok berhasil disimpan']);
    }

    public function edit_ajax($id)
    {
        $stok = StokModel::find($id);
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.edit_ajax', compact('stok', 'barangs', 'users'));
    }

    public function update_ajax(Request $request, $id)
    {
        $rules = [
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors()]);
        }

        $stok = StokModel::find($id);
        $stok->update($request->all());

        return response()->json(['status' => true, 'message' => 'Data stok berhasil diperbarui']);
    }

    public function confirm_ajax($id)
    {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', compact('stok'));
    }

    public function delete_ajax(Request $request, $id)
    {
        $stok = StokModel::find($id);
        $stok->delete();

        return response()->json(['status' => true, 'message' => 'Data stok berhasil dihapus']);
    }

    public function export_excel()
    {
        $stoks = StokModel::with('barang', 'user')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Barang');
        $sheet->setCellValue('B1', 'User');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($stoks as $s) {
            $sheet->setCellValue('A'.$rowNum, $s->barang->barang_nama);
            $sheet->setCellValue('B'.$rowNum, $s->user->nama);
            $sheet->setCellValue('C'.$rowNum, $s->stok_tanggal);
            $sheet->setCellValue('D'.$rowNum, $s->stok_jumlah);
            $rowNum++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Stok_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // public function export_pdf()
    // {
    //     $stoks = StokModel::with('barang', 'user')->get();
    //     $pdf = Pdf::loadView('stok.export_pdf', compact('stoks'));
    //     $pdf->setPaper('a4', 'portrait')->setOption('isRemoteEnabled', true);
    //     return $pdf->stream('Stok_' . date('Y-m-d_H-i-s') . '.pdf');
    // }
    public function export_pdf()
    {
        try {
            $stok = StokModel::with(['barang', 'user'])
                      ->orderBy('stok_tanggal', 'desc')
                      ->get();

            if ($stok->isEmpty()) {
                throw new \Exception("No stock data available for export");
            }

            return Pdf::loadView('stok.export_pdf', compact('stok'))
                      ->stream('Stock_Report.pdf');

        } catch (\Exception $e) {
            return redirect('/stok')
                   ->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_stok' => 'required|mimes:xls,xlsx|max:1024'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors()]);
        }

        $path = $request->file('file_stok')->store('temp');
        try {
            $sheet = IOFactory::load(storage_path('app/' . $path))->getActiveSheet();
            $rows = $sheet->toArray();
            foreach (array_slice($rows, 1) as $row) {
                StokModel::create([
                    'barang_id' => $row[0],
                    'user_id' => $row[1],
                    'stok_tanggal' => $row[2],
                    'stok_jumlah' => $row[3],
                ]);
            }
            Storage::delete($path);
            return response()->json(['status' => true, 'message' => 'Import data stok berhasil']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }
}

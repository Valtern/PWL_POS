<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    public function index()
    {
        $activeMenu = 'kategori';
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list'  => ['Home', 'Kategori'],
        ];
        return view('kategori.index', compact('activeMenu','breadcrumb'));
    }

    public function list(Request $request)
    {
        $query = KategoriModel::select('kategori_id','kategori_kode','kategori_nama','created_at');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function($k) {
                $id = $k->kategori_id;
                $btn  = "<button onclick=\"modalAction('" . url("/kategori/$id/show_ajax") . "')\" class=\"btn btn-info btn-sm\">Detail</button> ";
                $btn .= "<button onclick=\"modalAction('" . url("/kategori/$id/edit_ajax") . "')\" class=\"btn btn-warning btn-sm\">Edit</button> ";
                $btn .= "<button onclick=\"modalAction('" . url("/kategori/$id/delete_ajax") . "')\" class=\"btn btn-danger btn-sm\">Delete</button>";
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) ['title'=>'Tambah Kategori','list'=>['Home','Kategori','Tambah']];
        $page       = (object) ['title'=>'Tambah Kategori Baru'];
        $activeMenu = 'kategori';
        return view('kategori.create', compact('breadcrumb','page','activeMenu'));
    }

    public function show(string $id)
    {
        $kategori = KategoriModel::findOrFail($id);
        $breadcrumb = (object) ['title'=>'Detail Kategori','list'=>['Home','Kategori','Detail']];
        $page       = (object) ['title'=>'Detail Kategori'];
        $activeMenu = 'kategori';
        return view('kategori.show', compact('breadcrumb','page','activeMenu','kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100',
        ]);
        KategoriModel::create($data);
        return redirect('/kategori')->with('success','Kategori berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        $kategori = KategoriModel::findOrFail($id);
        $breadcrumb = (object) ['title'=>'Edit Kategori','list'=>['Home','Kategori','Edit']];
        $page       = (object) ['title'=>'Edit Kategori'];
        $activeMenu = 'kategori';
        return view('kategori.edit', compact('breadcrumb','page','activeMenu','kategori'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'kategori_kode' => "required|string|max:10|unique:m_kategori,kategori_kode,$id,kategori_id",
            'kategori_nama' => 'required|string|max:100',
        ]);
        $kategori = KategoriModel::findOrFail($id);
        $kategori->update($data);
        return redirect('/kategori')->with('success','Kategori berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $kategori = KategoriModel::findOrFail($id);
        try {
            $kategori->delete();
            return redirect('/kategori')->with('success','Kategori berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect('/kategori')->with('error','Kategori gagal dihapus: terkait entitas lain.');
        }
    }

    public function create_ajax()
    {
        return view('kategori.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100',
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message'=>'Validasi gagal','msgField'=>$validator->errors()]);
            }
            KategoriModel::create($validator->validated());
            return response()->json(['status'=>true,'message'=>'Kategori berhasil disimpan']);
        }
        return redirect('/kategori');
    }

    public function show_ajax(string $id)
    {
        $kategori = KategoriModel::findOrFail($id);
        return view('kategori.show_ajax', compact('kategori'));
    }

    public function edit_ajax(string $id)
    {
        $kategori = KategoriModel::findOrFail($id);
        return view('kategori.edit_ajax', compact('kategori'));
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'kategori_kode' => "required|string|max:10|unique:m_kategori,kategori_kode,$id,kategori_id",
                'kategori_nama' => 'required|string|max:100',
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message'=>'Validasi gagal','msgField'=>$validator->errors()]);
            }
            $kategori = KategoriModel::findOrFail($id);
            $kategori->update($validator->validated());
            return response()->json(['status'=>true,'message'=>'Kategori berhasil diperbarui']);
        }
        return redirect('/kategori');
    }

    public function confirm_ajax(string $id)
    {
        $kategori = KategoriModel::findOrFail($id);
        return view('kategori.confirm_ajax', compact('kategori'));
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax()) {
            $kategori = KategoriModel::find($id);
            if (!$kategori) {
                return response()->json(['status'=>false,'message'=>'Kategori tidak ditemukan']);
            }
            $kategori->delete();
            return response()->json(['status'=>true,'message'=>'Kategori berhasil dihapus']);
        }
        return redirect('/kategori');
    }

    public function import()
    {
        return view('kategori.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'file_kategori' => 'required|mimes:xls,xlsx|max:1024',
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message'=>'Validasi file gagal','msgField'=>$validator->errors()]);
            }
            $file = $request->file('file_kategori');
            $path = $file->store('temp');
            try {
                $sheet = IOFactory::load(storage_path("app/".$path))->getActiveSheet();
                $rows = $sheet->toArray();
                foreach (array_slice($rows,1) as $row) {
                    KategoriModel::create([
                        'kategori_kode' => $row[0],
                        'kategori_nama' => $row[1],
                    ]);
                }
                Storage::delete($path);
                return response()->json(['status'=>true,'message'=>'Category imported successfully']);
            } catch (\Exception $e) {
                return response()->json(['status'=>false,'message'=>'Failed to read file: '.$e->getMessage()]);
            }
        }
        return redirect('/kategori');
    }

    public function export_excel()
    {
        $data = KategoriModel::orderBy('kategori_id')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1','ID');
        $sheet->setCellValue('B1','Kode');
        $sheet->setCellValue('C1','Nama');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $row=2;
        foreach ($data as $d) {
            $sheet->setCellValue('A'.$row,$d->kategori_id);
            $sheet->setCellValue('B'.$row,$d->kategori_kode);
            $sheet->setCellValue('C'.$row,$d->kategori_nama);
            $row++;
        }
        foreach(range('A','C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $writer = IOFactory::createWriter($spreadsheet,'Xlsx');
        $filename = 'Data Barang ' . date('Y-m-d H:i:s') . '.xlsx';

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$file.'"');
        // header('Cache-Control: max-age=0');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $data = KategoriModel::orderBy('kategori_id')->get();
        $pdf = Pdf::loadView('kategori.export_pdf', ['kategori'=>$data]);
        $pdf->setPaper('a4','portrait')->setOption('isRemoteEnabled',true);
        return $pdf->stream('Kategori_'.date('Ymd_His').'.pdf');
    }
}

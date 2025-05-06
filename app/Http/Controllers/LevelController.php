<?php

namespace App\Http\Controllers;

use App\Http\Requests\LevelRequest;
use App\Models\Level;
use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LevelController extends Controller
{
    public function index()
    {
        $activeMenu = 'level';
        $breadcrumb = (object) [
            'title' => 'Level Management',
            'list' => ['Home', 'Level']
        ];

        return view('level.index', [
            'activeMenu' => $activeMenu,
            'breadcrumb' => $breadcrumb
        ]);
    }

    public function list(Request $request)
    {
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('action', function ($level) {
                $btn = '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Delete</button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_kode' => 'required|string|max:10|unique:m_level,level_kode',
            'level_nama' => 'required|string|max:100'
        ]);

        LevelModel::create($request->all());

        return redirect('/level')->with('success', 'Level created successfully!');
    }

    public function edit(string $id)
    {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit']
        ];

        $page = (object) ['title' => 'Edit Level'];
        $activeMenu = 'level';

        return view('level.edit', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'level_kode' => 'required|string|max:10|unique:m_level,level_kode,'.$id.',level_id',
            'level_nama' => 'required|string|max:100'
        ]);

        $level = LevelModel::find($id);
        $level->update($request->all());

        return redirect('/level')->with('success', 'Level updated successfully!');
    }

    public function destroy(string $id)
    {
        $level = LevelModel::find($id);
        if(!$level) {
            return redirect('/level')->with('error', 'Level not found!');
        }

        try {
            $level->delete();
            return redirect('/level')->with('success', 'Level deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/level')->with('error', 'Cannot delete level - related data exists!');
        }
    }

    public function create_ajax()
    {
        return view('level.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'level_kode' => 'required|string|max:10|unique:m_level,level_kode',
                'level_nama' => 'required|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ]);
            }

            LevelModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Level created successfully'
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $level = LevelModel::find($id);
        return view('level.edit_ajax', ['level' => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'level_kode' => 'required|string|max:10|unique:m_level,level_kode,'.$id.',level_id',
                'level_nama' => 'required|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ]);
            }

            LevelModel::find($id)->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Level updated successfully'
            ]);
        }
        return redirect('/');
    }

    public function import()
    {
        return view('level.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'file_level' => 'required|mimes:xlsx|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid file',
                    'errors' => $validator->errors()
                ]);
            }

            try {
                $file = $request->file('file_level');
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();

                foreach ($sheet->toArray() as $row => $data) {
                    if ($row === 0) continue; // Skip header

                    LevelModel::updateOrCreate(
                        ['level_kode' => $data[0]],
                        ['level_nama' => $data[1]]
                    );
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Levels imported successfully'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Import failed: ' . $e->getMessage()
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $levels = LevelModel::all();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Level Code');
        $sheet->setCellValue('B1', 'Level Name');

        $sheet->getStyle('A1:B1')->getFont()->setBold(true); // bold header

        // Populate data
        $row = 2;
        foreach ($levels as $level) {
            $sheet->setCellValue('A'.$row, $level->level_kode);
            $sheet->setCellValue('B'.$row, $level->level_nama);
            $row++;
        }
        $sheet->setTitle('Data Level'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Level ' . date('Y-m-d H:i:s') . '.xlsx';

        // Set file headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');;

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $levels = LevelModel::all();
        $pdf = Pdf::loadView('level.export_pdf', ['levels' => $levels]);
        return $pdf->stream('levels_export.pdf');
    }
    // public function index() {
    //     // DB::insert('insert into m_level(level_kode, level_nama, created_at) values(?, ?, ?)', ['CUS', 'Pelanggan', now()]);
    //     // return 'Insert data baru berhasil';

    //     // $row = DB::update('update m_level set level_nama = ? where level_kode = ?', ['Customer', 'CUS']);
    //     // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row.' baris';

    //     // $row = DB::delete('delete from m_level where level_kode = ?', ['CUS']);
    //     // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row.' baris';

    //     // $data = DB::select('select * from m_level');
    //     // return view('level', ['data' => $data]);

    //     $breadcrumb = (object) [
    //         'title' => 'Daftar Level',
    //         'list' => ['Home', 'Level']
    //     ];

    //     $page = (object) [
    //         'title' => 'Daftar level dalam sistem'
    //     ];

    //     $activeMenu = 'level';
    //     return view('level.index', compact('breadcrumb', 'page', 'activeMenu'));
    // }

    // // public function list(Request $request)
    // // {
    // //     $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

    // //     return DataTables::of($levels)
    // //         ->addIndexColumn()
    // //         ->addColumn('action', function ($level) {
    // //             $btn = '<a href="' . url('/level/' . $level->level_id) . '" class="btn btn-info btn-sm">Detail</a> ';
    // //             $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
    // //             $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $level->level_id) . '">' .
    // //                 csrf_field() . method_field('DELETE') .
    // //                 '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this level?\');">Delete</button></form>';
    // //             return $btn;
    // //         })
    // //         ->rawColumns(['action'])
    // //         ->make(true);
    // // }

    // public function create()
    // {
    //     $breadcrumb = (object) [
    //         'title' => 'Tambah Level',
    //         'list' => ['Home', 'Level', 'Tambah']
    //     ];

    //     $page = (object) [
    //         'title' => 'Tambah level baru'
    //     ];

    //     $activeMenu = 'level';
    //     return view('level.create', compact('breadcrumb', 'page', 'activeMenu'));
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'level_kode' => 'required|string|unique:m_level,level_kode',
    //         'level_nama' => 'required|string|max:100'
    //     ]);

    //     LevelModel::create($request->only(['level_kode', 'level_nama']));

    //     return redirect('/level')->with('success', 'Data level berhasil disimpan');
    // }

    // public function show(string $id)
    // {
    //     $level = LevelModel::findOrFail($id);

    //     $breadcrumb = (object) [
    //         'title' => 'Detail Level',
    //         'list' => ['Home', 'Level', 'Detail']
    //     ];

    //     $page = (object) [
    //         'title' => 'Detail level'
    //     ];

    //     $activeMenu = 'level';
    //     return view('level.show', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    // }

    // public function edit(string $id)
    // {
    //     $level = LevelModel::findOrFail($id);

    //     $breadcrumb = (object) [
    //         'title' => 'Edit Level',
    //         'list' => ['Home', 'Level', 'Edit']
    //     ];

    //     $page = (object) [
    //         'title' => 'Edit Level'
    //     ];

    //     $activeMenu = 'level';
    //     return view('level.edit', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    // }

    // public function update(Request $request, string $id)
    // {
    //     $request->validate([
    //         'level_kode' => 'required|string|unique:m_level,level_kode,' . $id . ',level_id',
    //         'level_nama' => 'required|string|max:100'
    //     ]);

    //     LevelModel::findOrFail($id)->update($request->only(['level_kode', 'level_nama']));

    //     return redirect('/level')->with('success', 'Data level berhasil diubah');
    // }

    // public function destroy(string $id)
    // {
    //     $check = LevelModel::find($id);
    //     if (!$check) {
    //         return redirect('/level')->with('error', 'Data level tidak ditemukan');
    //     }

    //     try {
    //         LevelModel::destroy($id);
    //         return redirect('/level')->with('success', 'Data level berhasil dihapus');
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
    //     }
    // }

    // public function create_ajax()
    // {
    //     return view('level.create_ajax');
    // }

    // public function store_ajax(Request $request)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'level_kode' => 'required|string|min:3|unique:m_level,level_kode',
    //             'level_nama' => 'required|string|max:100',
    //         ];

    //         $validator = Validator::make($request->all(), $rules);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validation failed',
    //                 'errors' => $validator->errors(),
    //             ]);
    //         }

    //         LevelModel::create($request->all());

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Data level berhasil disimpan',
    //         ]);
    //     }
    //     return redirect('/');
    // }

    // public function list(Request $request)
    // {
    //     $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

    //     return DataTables::of($levels)
    //         ->addIndexColumn()
    //         ->addColumn('action', function ($level) {
    //             $btn = '<button onclick="modalAction(\''.url('/level/'.$level->level_id.'/show_ajax').'\')"
    //                         class="btn btn-info btn-sm">Detail</button> ';
    //             $btn .= '<button onclick="modalAction(\''.url('/level/'.$level->level_id.'/edit_ajax').'\')"
    //                         class="btn btn-warning btn-sm">Edit</button> ';
    //             $btn .= '<button onclick="modalAction(\''.url('/level/'.$level->level_id.'/delete_ajax').'\')"
    //                         class="btn btn-danger btn-sm">Delete</button>';

    //             return $btn;
    //         })
    //         ->rawColumns(['action'])
    //         ->make(true);
    // }

    // public function edit_ajax($id)
    // {
    //     $level = LevelModel::find($id);
    //     return view('level.edit_ajax', ['level' => $level]);
    // }

    // public function update_ajax(Request $request, $id)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'level_kode' => 'required|string|min:3|unique:m_level,level_kode,'.$id.',level_id',
    //             'level_nama' => 'required|string|max:100',
    //         ];

    //         $validator = Validator::make($request->all(), $rules);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validation failed',
    //                 'errors' => $validator->errors(),
    //             ]);
    //         }

    //         $level = LevelModel::find($id);

    //         if ($level) {
    //             $level->update($request->all());
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Data level berhasil diupdate'
    //             ]);
    //         }

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data tidak ditemukan'
    //         ]);
    //     }
    //     return redirect('/');
    // }

    // public function confirm_ajax($id)
    // {
    //     $level = LevelModel::find($id);
    //     return view('level.confirm_ajax', ['level' => $level]);
    // }

    // public function delete_ajax(Request $request)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $level = LevelModel::find($request->id);

    //         if ($level) {
    //             try {
    //                 $level->delete();
    //                 return response()->json([
    //                     'status' => true,
    //                     'message' => 'Data level berhasil dihapus'
    //                 ]);
    //             } catch (\Exception $e) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Gagal menghapus data: ' . $e->getMessage()
    //                 ]);
    //             }
    //         }

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data tidak ditemukan'
    //         ]);
    //     }
    //     return redirect('/');
    // }
}

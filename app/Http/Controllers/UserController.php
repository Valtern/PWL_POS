<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Display list view
    public function index()
    {
        $activeMenu = 'user';
        $breadcrumb = (object) [
            'title' => 'Daftar Pengguna',
            'list'  => ['Home', 'User']
        ];
        // Load levels for filter/dropdown
        $levels = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.index', compact('activeMenu', 'breadcrumb', 'levels'));
    }

    // Show create form
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list'  => ['Home', 'User', 'Tambah']
        ];
        $page = (object) [
            'title' => 'Tambah Pengguna Baru'
        ];
        $activeMenu = 'user';
        $levels = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.create', compact('breadcrumb', 'page', 'activeMenu', 'levels'));
    }

    // Show detail view
    public function show(string $id)
    {
        $user = UserModel::find($id);
        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list'  => ['Home', 'User', 'Detail']
        ];
        $page = (object) [
            'title' => 'Detail Pengguna'
        ];
        $activeMenu = 'user';

        return view('user.show', compact('breadcrumb', 'page', 'activeMenu', 'user'));
    }

    // DataTables AJAX list
    // public function list(Request $request)
    // {
    //     $query = UserModel::select('user_id', 'level_id', 'username', 'nama', 'created_at')
    //         ->with('level');

    //     // Optional filter by level
    //     if ($request->filled('filter_level')) {
    //         $query->where('level_id', $request->input('filter_level'));
    //     }

    //     return DataTables::of($query)
    //         ->addIndexColumn()
    //         ->addColumn('level_nama', fn($u) => $u->level->level_nama)
    //         ->addColumn('action', function($u) {
    //             $btn = '';
    //             $btn .= '<button onclick="modalAction(\'' . url('/user/' . $u->user_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
    //             $btn .= '<button onclick="modalAction(\'' . url('/user/' . $u->user_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
    //             $btn .= '<button onclick="modalAction(\'' . url('/user/' . $u->user_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Delete</button> ';
    //             return $btn;
    //         })
    //         ->rawColumns(['action'])
    //         ->make(true);
    // }

    // Store standard form
    public function store(Request $request)
    {
        $request->validate([
            'level_id' => 'required|exists:m_level,level_id',
            'username' => 'required|string|unique:m_user,username|min:4|max:20',
            'nama'     => 'required|string|max:100',
            'password' => 'required|string|min:6'
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));
        UserModel::create($data);

        return redirect('/user')->with('success', 'User berhasil ditambahkan!');
    }

    // Show edit form
    public function edit(string $id)
    {
        $user = UserModel::find($id);
        if (!$user) {
            return redirect('/user')->with('error', 'User tidak ditemukan.');
        }
        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list'  => ['Home', 'User', 'Edit']
        ];
        $page = (object) [
            'title' => 'Edit Pengguna'
        ];
        $activeMenu = 'user';
        $levels = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.edit', compact('breadcrumb', 'page', 'activeMenu', 'user', 'levels'));
    }

    // Update standard form
    public function update(Request $request, string $id)
    {
        $request->validate([
            'level_id' => 'required|exists:m_level,level_id',
            'username' => 'required|string|unique:m_user,username,' . $id . ',user_id|min:4|max:20',
            'nama'     => 'required|string|max:100',
            'password' => 'nullable|string|min:6'
        ]);

        $user = UserModel::find($id);
        if (!$user) {
            return redirect('/user')->with('error', 'User tidak ditemukan.');
        }

        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect('/user')->with('success', 'User berhasil diperbarui!');
    }

    // Delete standard
    public function destroy(string $id)
    {
        $user = UserModel::find($id);
        if (!$user) {
            return redirect('/user')->with('error', 'User tidak ditemukan.');
        }
        try {
            $user->delete();
            return redirect('/user')->with('success', 'User berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/user')->with('error', 'User gagal dihapus karena terkait data lain.');
        }
    }

    // AJAX: create form
    // public function create_ajax()
    // {
    //     $levels = LevelModel::select('level_id', 'level_nama')->get();
    //     return view('user.create_ajax', compact('levels'));
    // }

    // // AJAX: store
    // public function store_ajax(Request $request)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'level_id' => 'required|integer|exists:m_level,level_id',
    //             'username' => 'required|string|max:20|unique:m_user,username',
    //             'nama'     => 'required|string|max:100',
    //             'password' => 'required|string|min:6'
    //         ];
    //         $validator = Validator::make($request->all(), $rules);
    //         if ($validator->fails()) {
    //             return response()->json([ 'status' => false, 'message' => 'Validasi gagal', 'msgField' => $validator->errors() ]);
    //         }
    //         $data = $request->all();
    //         $data['password'] = Hash::make($data['password']);
    //         UserModel::create($data);
    //         return response()->json([ 'status' => true, 'message' => 'User berhasil disimpan' ]);
    //     }
    //     return redirect('/');
    // }

    // // AJAX: edit form
    // public function edit_ajax(string $id)
    // {
    //     $user = UserModel::find($id);
    //     $levels = LevelModel::select('level_id','level_nama')->get();
    //     return view('user.edit_ajax', compact('user', 'levels'));
    // }

    // // AJAX: update
    // public function update_ajax(Request $request, $id)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'level_id' => 'required|integer|exists:m_level,level_id',
    //             'username' => 'required|string|max:20|unique:m_user,username,' . $id . ',user_id',
    //             'nama'     => 'required|string|max:100',
    //             'password' => 'nullable|string|min:6'
    //         ];
    //         $validator = Validator::make($request->all(), $rules);
    //         if ($validator->fails()) {
    //             return response()->json([ 'status' => false, 'message' => 'Validasi gagal', 'msgField' => $validator->errors() ]);
    //         }
    //         $user = UserModel::find($id);
    //         if (!$user) {
    //             return response()->json([ 'status' => false, 'message' => 'User tidak ditemukan' ]);
    //         }
    //         $data = $request->all();
    //         if ($request->filled('password')) {
    //             $data['password'] = Hash::make($data['password']);
    //         } else {
    //             unset($data['password']);
    //         }
    //         $user->update($data);
    //         return response()->json([ 'status' => true, 'message' => 'User berhasil diperbarui' ]);
    //     }
    //     return redirect('/');
    // }

    // // AJAX: confirm delete
    // public function confirm_ajax(string $id)
    // {
    //     $user = UserModel::find($id);
    //     return view('user.confirm_ajax', compact('user'));
    // }

    // // AJAX: delete
    // public function delete_ajax(Request $request, $id)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $user = UserModel::find($id);
    //         if (!$user) {
    //             return response()->json([ 'status' => false, 'message' => 'User tidak ditemukan' ]);
    //         }
    //         $user->delete();
    //         return response()->json([ 'status' => true, 'message' => 'User berhasil dihapus' ]);
    //     }
    //     return redirect('/');
    // }

    // View import form
    public function import()
    {
        return view('user.import');
    }

    // AJAX: import
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [ 'file_user' => 'required|mimes:xls,xlsx|max:1024' ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([ 'status' => false, 'message' => 'Validasi file gagal', 'msgField' => $validator->errors() ]);
            }
            $file = $request->file('file_user');
            $path = $file->store('temp');
            try {
                $sheet = IOFactory::load(storage_path('app/' . $path))->getActiveSheet();
                $rows = $sheet->toArray();
                foreach (array_slice($rows, 1) as $row) {
                    UserModel::create([
                        'level_id' => $row[0],
                        'username' => $row[1],
                        'nama'     => $row[2],
                        'password' => Hash::make($row[3] ?? 'password123'),
                    ]);
                }
                Storage::delete($path);
                return response()->json([ 'status' => true, 'message' => 'Users imported successfully' ]);
            } catch (\Exception $e) {
                return response()->json([ 'status' => false, 'message' => 'Failed to read file: ' . $e->getMessage() ]);
            }
        }
        return redirect('/');
    }

    // Export Excel
    public function export_excel()
    {
        $users = UserModel::with('level')->orderBy('level_id')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Level ID');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Created At');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($users as $u) {
            $sheet->setCellValue('A'.$rowNum, $u->level->level_id);
            $sheet->setCellValue('B'.$rowNum, $u->username);
            $sheet->setCellValue('C'.$rowNum, $u->nama);
            $sheet->setCellValue('D'.$rowNum, $u->created_at);
            $rowNum++;
        }
        foreach (range('A','D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Users '.date('Y-m-d_H-i-s').'.xlsx';

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$filename.'"');
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

    // Export PDF
    public function export_pdf()
    {
        $users = UserModel::with('level')->orderBy('level_id')->get();
        $pdf   = Pdf::loadView('user.export_pdf', compact('users'));
        $pdf->setPaper('a4','portrait')->setOption('isRemoteEnabled', true);
        return $pdf->stream('Users_'.date('Y-m-d_H-i-s').'.pdf');
    }


// namespace App\Http\Controllers;

// use App\Models\LevelModel;
// use App\Models\UserModel;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Yajra\DataTables\Facades\DataTables;
// use Illuminate\Support\Facades\Validator;

// class UserController extends Controller
// {
//     public function index(){
//         $breadcrumb = (object) [
//             "title" => "Daftar User",
//             "list" => ['Home', 'User']
//         ];

//         $page = (object) [
//             "title" => "Daftar user yang terdaftar dalam sistem"
//         ];

//         $activeMenu = 'user';
//         $level = LevelModel::all();
//         return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);

        // $breadcrumb = (object)[
        //     'title' => 'Daftar user',
        //     'list' => ['Home', 'User']
        // ];
        // $page=(object)[
        //     'title' => 'Daftar user yang terdaftar dalam sistem'
        // ];
        // $activeMenu = 'user';
        // return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);

        // $user = UserModel::with('level')->get();
        // return view('user', ['data' => $user]);

        // $user = UserModel::with('level')->get();
        // dd($user);

        // $user = UserModel::all();
        // return view('user', ['data' => $user]);

        // $user = UserModel::create([
        //     'username' => 'manager11',
        //     'nama' => 'Manager11',
        //     'password' => Hash::make('12345'),
        //     'level_id' => 2,
        // ]);
        // $user->username = 'manager12';
        // $user->save();
        // $user->wasChanged(); //true
        // $user->wasChanged('username'); //true
        // $user->wasChanged(['username', 'level_id']); //true
        // $user->wasChanged('nama'); //false
        // dd($user->wasChanged(['nama', 'username'])); //true

        // $user = UserModel::firstOrNew(
        //     [
        //         'username' => 'manager55',
        //         'nama' => 'Manager55',
        //         'password' => Hash::make('12345'),
        //         'level_id' => 2
        //     ],
        // );
        // $user->username = 'manager56';
        // //isDity
        // $user->isDirty(); //true
        // $user->isDirty('username'); //true
        // $user->isDirty('nama'); //false
        // $user->isDirty(['nama', 'username']); //true
        // //isClean
        // $user->isClean(); //false
        // $user->isClean('username'); //false
        // $user->isClean('nama'); //true
        // $user->isClean(['nama', 'username']); //false
        // //
        // $user->save();
        // //
        // $user->isDirty(); //false
        // $user->isClean(); //true
        // dd($user->isDirty());

        // return view('user', ['data' => $user]);

        // $user = UserModel::firstOrCreate(
        //     [
        //         'username' => 'manager22',
        //         'nama' => 'Manager Dua Dua',
        //         'password' => Hash::make('12345'),
        //         'level_id' => 2
        //     ],
        // );
        // return view('user', ['data' => $user]);

        // $user = UserModel::where('level_id', 2)->count();
        // //dd($user);
        // return view('user', ['data' => $user]);

        // $user = UserModel::where('username', 'manager9')->firstOrFail();
        // return view('user', ['data' => $user]);

        // $user = UserModel::findOrFail(1);
        // return view('user', ['data' => $user]);

        // $user = UserModel::findOr(20, ['username', 'nama'], function(){
        //     abort(404);
        // });
        // return view('user', ['data' => $user]);

        // $user = UserModel::firstWhere('level_id', 1);
        // return view('user', ['data' => $user]);

        // $user = UserModel::where('level_id', 1)->first();
        // return view('user', ['data' => $user]);

        // $user = UserModel::find(1);
        // return view('user', ['data' => $user]);

        // $data = [
        //     'level_id' => 2,
        //     'username' => 'manager_tiga',
        //     'nama' => 'Manager 3',
        //     'password' => Hash::make('12345')
        // ];
        // UserModel::create($data);
        // $user = UserModel::all();
        // return view('user', ['data' => $user]);

        // $data = [
        //     'nama' => 'Pelanggan pertama',
        // ];
        // UserModel::where('username', 'customer-1')->update($data);

        // $user = UserModel::all();
        // return view('user', ['data' => $user]);

        // $data = [
        //     'username' => 'customer-1',
        //     'nama' => 'Pelanggan',
        //     'password' => Hash::make('12345'),
        //     'level_id' => 4
        // ];

        // // Cek apakah username sudah ada
        // $checkUser = UserModel::where('username', $data['username'])->first();

        // if (!$checkUser) {
        //     UserModel::insert($data);
        // }

        // $user = UserModel::all();
        // return view('user', ['data' => $user]);

        // $data = [
        //     'username' => 'customer-1',
        //     'nama' => 'Pelanggan',
        //     'password' => Hash::make('12345'),
        //     'level_id' => 4
        // ];
        // UserModel::insert($data);

        // $user = UserModel::all();
        // return view('user', ['data' => $user]);
//     }

//     public function tambah(){
//         return view('user_tambah');
//     }

//     public function tambah_simpan(Request $request){
//         UserModel::create([
//             'username' => $request->username,
//             'nama' => $request->nama,
//             'password' => Hash::make('$request->password'),
//             'level_id' => $request->level_id
//         ]);
//         return redirect('/user');
//     }

//     public function ubah($id){
//         $user = UserModel::find($id);
//         return view('user_ubah', ['data' => $user]);
//     }

//     public function ubah_simpan($id, Request $request){
//         $user = UserModel::find($id);
//         $user->username = $request->username;
//         $user->nama = $request->nama;
//         $user->level_id = $request->level_id;
//         $user->save();
//         return redirect('/user');
//     }

//     public function hapus($id){
//         $user = UserModel::find($id);
//         $user->delete();
//         return redirect('/user');
//     }

// // Fetch user data in JSON form for DataTables
//     // public function list(Request $request)
//     // {
//     //     $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
//     //         ->with('level');

//     //     if ($request->level_id) {
//     //         $users->where('level_id', $request->level_id);
//     //     }

//     //     return DataTables::of($users)
//     //         ->addIndexColumn()
//     //         ->addColumn('action', function ($user) { // add action column
//     //             $btn = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btn-sm">Detail</a> ';
//     //             $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
//     //             $btn .= '<form class="d-inline-block" method="POST" action="'. url('/user/'.$user->user_id) .'">'
//     //                 . csrf_field() . method_field('DELETE') .
//     //                 '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Did you delete this data?\');">Delete</button></form>';
//     //             return $btn;
//     //         })
//     //         ->rawColumns(['action'])
//     //         ->make(true);
//     // }

//     public function create() {
//         $breadcrumb = (object) [
//             'title' => 'Tambah User',
//             'list' => ['Home', 'User', 'Tambah']
//         ];

//         $page = (object)[
//             'title' => 'Tambah user baru'
//         ];

//         $level = LevelModel::all();
//         $activeMenu = 'user';

//         return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
//     }

//     public function store(Request $request) {
//         $request->validate([
//             'username' => 'required|string|min:3|unique:m_user,username',
//             'nama' => 'required|string|max:100',
//             'password' => 'required|min:5',
//             'level_id' => 'required|integer'
//         ]);

//         UserModel::create([
//             'username' => $request->username,
//             'nama' => $request->nama,
//             'password' => bcrypt($request->password),
//             'level_id' => $request->level_id
//         ]);

//         return redirect('/user')->with('success', 'Data user berhasil disimpan');
//     }

//     public function show(string $id) {
//         $user = UserModel::with('level')->find($id);

//         $breadcrumb = (object)[
//             'title' => 'Detail User',
//             'list' => ['Home', 'User', 'Detail']
//         ];

//         $page = (object)[
//             'title' => 'Detail user'
//         ];

//         $activeMenu = 'user';
//         return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
//     }

//     public function edit(string $id)
//     {
//         $user = UserModel::find($id);
//         $level = LevelModel::all();

//         $breadcrumb = (object) [
//             'title' => 'Edit User',
//             'list' => ['Home', 'User', 'Edit']
//         ];

//         $page = (object) [
//             'title' => 'Edit User'
//         ];

//         $activeMenu = 'user';
//         $level = LevelModel::all();

//         return view('user.edit', [
//             'breadcrumb' => $breadcrumb,
//             'page' => $page,
//             'user' => $user,
//             'level' => $level,
//             'activeMenu' => $activeMenu
//         ]);
//     }

//     public function update(Request $request, string $id)
//     {
//         $request->validate([
//             'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
//             'nama' => 'required|string|max:100',
//             'password' => 'nullable|min:5',
//             'level_id' => 'required|integer'
//         ]);

//         UserModel::find($id)->update([
//             'username' => $request->username,
//             'nama' => $request->nama,
//             'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
//             'level_id' => $request->level_id
//         ]);

//         return redirect('/user')->with('success', 'Data user berhasil diubah');
//     }

//     public function destroy(string $id)
//     {
//         $check = UserModel::find($id);
//         if (!$check) {
//             return redirect('/user')->with('error', 'Data user tidak ditemukan');
//         }

//         try {
//             UserModel::destroy($id);
//             return redirect('/user')->with('success', 'Data user berhasil dihapus');
//         } catch (\Illuminate\Database\QueryException $e) {
//             return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
//         }
//     }

    public function create_ajax(){
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.create_ajax')
                    ->with('level', $level);
    }

    public function store_ajax(Request $request){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
              'level_id' => 'required|integer',
              'username' => 'required|string|min:3|unique:m_user,username',
              'nama' => 'required|string|max:100',
              'password' => 'required|min:6',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            UserModel::create($request->all());
            return response()->json([
                'status' => 'true',
                'message' => 'Data user berhasil disimpan',
            ]);
        }
        redirect('/');
    }

    public function list(Request $request){
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
            ->with('level');

        // Filter user data by level_id
        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)
            ->addIndexColumn() // Adds index/no sort column (default column name: DT_RowIndex)
            ->addColumn('action', function ($user) {
                // Add action column with buttons
                $btn = '<button onclick="modalAction(\''.url('/user/'.$user->user_id.'/show_ajax').'\')"
                            class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/user/'.$user->user_id.'/edit_ajax').'\')"
                            class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/user/'.$user->user_id.'/delete_ajax').'\')"
                            class="btn btn-danger btn-sm">Delete</button>';

                return $btn;
            })
            ->rawColumns(['action']) // Ensures the action column is interpreted as HTML
            ->make(true);
    }

    public function edit_ajax($id){
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id){
        // Check if the request is from AJAX or wants JSON response
        if ($request->ajax() || $request->wantsJson()) {
            // Validation rules
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
                'nama' => 'required|max:100',
                'password' => 'nullable|min:6|max:20'
            ];
            // Validate request data
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // JSON response, false indicates failure
                    'message' => 'Validation failed.',
                    'msgField' => $validator->errors() // Fields with validation errors
                ]);
            }
            // Find the user by ID
            $user = UserModel::find($id);

            if ($user) {
                // If the password field is empty, remove it from the request
                if (!$request->filled('password')) {
                    $request->request->remove('password');
                }
                // Update user data
                $user->update($request->all());

                return response()->json([
                    'status' => true, // Success response
                    'message' => 'Data updated successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found'
                ]);
            }
        }
        // Redirect if the request is not AJAX
        return redirect('/');
    }

    public function confirm_ajax(string $id){
        $user = UserModel::find($id);
        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request){
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($request->id);
            if ($user) {
                $user->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function register(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama' => 'required|string|max:100',
                'password' => 'required|min:6'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            UserModel::create([
                'level_id' => $request->level_id,
                'username' => $request->username,
                'nama' => $request->nama,
                'password' => bcrypt($request->password) // Jangan lupa hashing password
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan',
                'redirect' => url('/login')
            ]);
        }

        // Jika request bukan AJAX
        return redirect('/login/');
    }

    public function showRegistrationForm()
{
    $breadcrumb = (object) [
        'title' => 'User Registration',
        'list' => ['Home', 'Register']
    ];

    $page = (object) [
        'title' => 'Create new user account'
    ];

    $levels = LevelModel::all();
    return view('auth.register', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'levels' => $levels
    ]);
}

public function uploadPhoto(Request $request)
{
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $file = $request->file('profile_photo');

    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
    $file->move(public_path('uploads/profile'), $filename);

    // Save to session
    session(['photo' => 'uploads/profile/' . $filename]);

    return redirect()->back()->with('success', 'Photo uploaded successfully!');
}

//     // public function show($id, $name){
//     //     // return view('user.profile', [
//     //     //     'id' => $id,
//     //     //     'name' => $name
//     //     // ]);
//     // }
// }
}

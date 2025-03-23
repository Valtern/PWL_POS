<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PenjualanModel;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar transaksi penjualan dalam sistem'
        ];

        $activeMenu = 'penjualan';
        return view('penjualan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal');

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn = '<a href="' . url('/penjualan/' . $data->penjualan_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/penjualan/' . $data->penjualan_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/penjualan/' . $data->penjualan_id) . '">' .
                    csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this record?\');">Delete</button></form>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Penjualan',
            'list' => ['Home', 'Penjualan', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah transaksi penjualan baru'
        ];

        $activeMenu = 'penjualan';
        return view('penjualan.create', compact('breadcrumb', 'page', 'activeMenu'));
    }

public function store(Request $request)
{
 
    $validated = $request->validate([
        'user_id' => 'required|integer|exists:m_user,user_id', // Ensure user_id exists in m_user table
        'pembeli' => 'required|string|max:255',
        'penjualan_kode' => 'required|string|max:255|unique:t_penjualan,penjualan_kode',
        'penjualan_tanggal' => 'required|date',
    ]);

    // Create the record
    try {
        PenjualanModel::create($validated);
        return redirect('penjualan')->with('success', 'Data added successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to add data: ' . $e->getMessage());
    }
}

    public function show(string $id)
    {
        $penjualan = PenjualanModel::findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Detail Penjualan',
            'list' => ['Home', 'Penjualan', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail transaksi penjualan'
        ];

        $activeMenu = 'penjualan';
        return view('penjualan.show', compact('breadcrumb', 'page', 'penjualan', 'activeMenu'));
    }

    public function edit(string $id)
    {
        $penjualan = PenjualanModel::findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit Penjualan',
            'list' => ['Home', 'Penjualan', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit transaksi penjualan'
        ];

        $activeMenu = 'penjualan';
        return view('penjualan.edit', compact('breadcrumb', 'page', 'penjualan', 'activeMenu'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pembeli' => 'required|string|max:100',
            'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode,' . $id . ',penjualan_id',
            'penjualan_tanggal' => 'required|date'
        ]);

        PenjualanModel::findOrFail($id)->update($request->only(['pembeli', 'penjualan_kode', 'penjualan_tanggal']));

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil diubah');
    }

    public function destroy(string $id)
    {
        $check = PenjualanModel::find($id);
        if (!$check) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        try {
            PenjualanModel::destroy($id);
            return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/penjualan')->with('error', 'Data penjualan gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}

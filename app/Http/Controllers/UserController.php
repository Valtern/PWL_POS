<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        //$user = UserModel::where('level_id', 1) ->first();

        //$user = UserModel::findOr(20, ['username', 'nama'], function () {
        //    abort(404);
       // });


       //$user = UserModel::where('username', 'manager9')->firstOrFail();

       /*$user = UserModel::where('level_id', 2)->count();
        return view('user', ['data' => $user]);*/

        /*$user = UserModel::firstOrCreate(
            [
                'username' => 'manager22',
                'nama' => 'Manager Dua Dua',
                'password'=> Hash::make('12345'),
                'level_id'=> 2
            ],
        );*/

       /* $user = UserModel::firstOrNew(
            [
                'username'=>'manager33',
                'nama'=>'Manager Tiga Tiga',
                'password'=> Hash::make('12345'),
                'level_id'=>2
            ],
        );
        $user->save();
        return view('user', ['data' => $user]);*/
      /*  $user = UserModel::create([
            'username' => 'manager44',
            'nama' => 'Manager44',
            'password' => Hash::make('12345'),
            'level_id' => 2,
        ]);

        $user->username = 'manager56';

        $user->isDirty(); // true
        $user->isDirty('username'); // true
        $user->isDirty('nama'); // false
        $user->isDirty(['nama', 'username']); // true

        $user->isClean(); // false
        $user->isClean('username'); // false
        $user->isClean('nama'); // true
        $user->isClean(['nama', 'username']); // false

        $user->save();

        $user->isDirty(); // false
        $user->isClean(); // true

        dd($user->isDirty());*/

        $user = UserModel::create([
            'username' => 'manager11',
            'nama' => 'Manager11',
            'password' => Hash::make('12345'),
            'level_id' => 2,
        ]);

        $user->username = 'manager12';

        $user->save();

        $user->wasChanged(); // true
        $user->wasChanged('username'); // true
        $user->wasChanged(['username', 'level_id']); // true
        $user->wasChanged('nama'); // false
        dd($user->wasChanged(['nama', 'username'])); // true





        // tambah data user dengan Eloquent Model
        /*$data = [
            'level_id' => 2,
            'username' => 'manager_tiga',
            'nama' => 'Manager 3',
            'password' => Hash::make('12345')
        ];*/

       // UserModel::create($data);

       // $user = UserModel::all();
        //return view('user', ['data' => $user]);



       // UserModel::where('username', 'customer-1')->update($data); // update data user
//asdasdasdsa
        // coba akses model UserModel
       // $user = UserModel::all(); // ambil semua data dari tabel m_user
        //return view('user', ['data' => $user]);
    }
}


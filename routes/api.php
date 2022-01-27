<?php

use App\Models\Saldo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('data_user')->group(function () {
    Route::get("/", function(){
        $users = User::all();

        return response()->json([
            "status" => 200,
            "data" => $users,
            "message" => "Berhasil mengambil data user"
        ]);
    });

    Route::post("/add", function(Request $request){
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'role_id' => ['required', 'numeric']
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role_id" => $request->role_id
        ]);

        if($user->role_id == 4){
            $saldo = Saldo::create([
                "user_id" => $user->id,
                "saldo" => 0
            ]);
        }

        return response()->json([
            "status" => 200,
            "message" => "Berhasil Menambahkan User & Mengisi Saldo Awal",
            "data" => [$user, $saldo]
        ]);
    });
});
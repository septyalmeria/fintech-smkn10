<?php

use App\Models\Barang;
use App\Models\Saldo;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post("/addUser", function(){});
Route::prefix('barang')->group(function () {
    Route::get('/', function () {
        // Matches The "/admin/users" URL
    });
    
    Route::get('/add', function () {
        // Matches The "/admin/users" URL
    });

    Route::post('/create', function () {
        // Matches The "/admin/users" URL
    });

    Route::get('/edit/{id}', function () {
        // Matches The "/admin/users" URL
    });

    Route::put('/update/{id}', function () {
        // Matches The "/admin/users" URL
    });

    Route::get('/delete/{id}', function () {
        // Matches The "/admin/users" URL
    });
});

Route::get("topup", function(){
    $saldo = Saldo::where("user_id", Auth::user()->id)->first();

    return view("topup", [
        "saldo" => $saldo
    ]);
})->name("topup");

Route::post("addToCart/{id}", function(Request $request){
    Transaksi::create([
        "user_id" => Auth::user()->id,
        "barang_id" => $request->barang_id,
        "status" => 1,
        "jumlah" => $request->jumlah,
        "type" => 2
    ]);

    return redirect()->back()->with("status", "Berhasil menambahkan barang ke keranjang");
})->name("addToCart");

Route::get("checkout", function(){
    $invoice_id = "INV_" . Auth::user()->id . now()->timestamp;

    Transaksi::where("user_id", Auth::user()->id)->update([
        "invoice_id" => $invoice_id,
        "status" => 2
    ]);

    return redirect()->back()->with("status", "Berhasil Checkout");
})->name("checkout");

Route::prefix('transaksi')->group(function () {
    Route::get('/', function () {
        $barangs = Barang::all();
        $carts = Transaksi::where("user_id", Auth::user()->id)->where("status", 1)->get();

        $total_cart = 0;

        foreach($carts as $cart){
            $total_cart += ($cart->barang->price * $cart->jumlah);
        }

        return view("transaksi", [
            "barangs" => $barangs,
            "carts" => $carts,
            "total_cart" => $total_cart
        ]);
    })->name("transaksi");
    
    Route::get('/add', function () {
        // Matches The "/admin/users" URL
    });

    Route::post('/create', function (Request $request) {
        if($request->type == 1){
            $invoice_id = "SAL_" . Auth::user()->id . now()->timestamp;

            $saldo = Saldo::where("user_id", Auth::user()->id)->first();

            Transaksi::create([
                "user_id" => Auth::user()->id,
                "jumlah" => $request->jumlah,
                "invoice_id" => $invoice_id,
                "type" => $request->type
            ]);

            Saldo::where("user_id", Auth::user()->id)->update([
                "saldo" => $saldo->saldo + $request->jumlah
            ]);

            return redirect()->back()->with("status", "Berhasil Topup Saldo");
        }
        
    })->name("transaksi.create");

    Route::get('/edit/{id}', function () {
        // Matches The "/admin/users" URL
    });

    Route::put('/update/{id}', function () {
        // Matches The "/admin/users" URL
    });

    Route::get('/delete/{id}', function () {
        // Matches The "/admin/users" URL
    });
});
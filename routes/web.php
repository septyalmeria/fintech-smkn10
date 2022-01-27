<?php

use App\Models\Barang;
use App\Models\Saldo;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

Route::get("topup/setuju/{transaksi_id}", function($transaksi_id){
    $transaksi = Transaksi::find($transaksi_id);
    
    $saldo = Saldo::where("user_id", $transaksi->user_id)->first();

    Saldo::where("user_id", $transaksi->user_id)->update([
        "saldo" => $saldo->saldo + $transaksi->jumlah
    ]);

    $transaksi->update([
        "status" => 3
    ]);

    return redirect()->back()->with("status", "Topup disetujui");
})->name("topup.setuju");

Route::get("topup/tolak/{transaksi_id}", function($transaksi_id){
    $transaksi = Transaksi::find($transaksi_id);

    $transaksi->delete();

    return redirect()->back()->with("status", "Topup ditolak");
})->name("topup.tolak");

Route::get("jajan/setuju/{invoice_id}", function($invoice_id){
    $transaksis = Transaksi::where("invoice_id", $invoice_id);

    $total_data = 0;

    foreach($transaksis->get() as $transaksi){
        $total_data += ($transaksi->jumlah * $transaksi->barang->price);
    }

    $transaksis->update([
        "status" => 4 //FINISHED
    ]);

    return redirect()->back()->with("status", "Jajan disetujui");
})->name("jajan.setuju");

Route::get("jajan/tolak/{invoice_id}", function($invoice_id){
    $transaksis = Transaksi::where("invoice_id", $invoice_id);

    $total_data = 0;

    foreach($transaksis->get() as $transaksi){
        $total_data += ($transaksi->jumlah * $transaksi->barang->price);
    }

    $saldo = Saldo::where("user_id", $transaksis->get()[0]->user_id)->first();

    $saldo->update([
        "saldo" => $saldo->saldo + $total_data
    ]);

    $transaksis->update([
        "status" => 5 //REJECTED
    ]);

    return redirect()->back()->with("status", "Jajan ditolak");
})->name("jajan.tolak");

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

    Transaksi::where("user_id", Auth::user()->id)->where("type", 2)->where("status", 1)->update([
        "invoice_id" => $invoice_id,
        "status" => 2
    ]);

    return redirect()->back()->with("status", "Berhasil Checkout");
})->name("checkout");

Route::get("bayar", function(){
    $datas = Transaksi::where("user_id", Auth::user()->id)
            ->where("type", 2)
            ->where("status", 2);

    $total_data = 0;

    foreach($datas->get() as $data){
        $total_data += ($data->barang->price * $data->jumlah);
    }

    $saldo = Saldo::where("user_id", Auth::user()->id)->first();

    $saldo->update([
        "saldo" => $saldo->saldo - $total_data
    ]);

    $datas->update([
        "status" => 3
    ]);

    return redirect()->back()->with("status", "Berhasil Bayar. Menunggu konfirmasi Kantin");
})->name("bayar");

Route::prefix('transaksi')->group(function () {
    Route::get('/', function () {
        $barangs = Barang::all();
        $carts = Transaksi::where("user_id", Auth::user()->id)->where("status", 1)->where("type", 2)->get();
        $checkouts = Transaksi::where("user_id", Auth::user()->id)->where("status", 2)->where("type", 2)->get();
        $saldo = Saldo::where("user_id", Auth::user()->id)->first();

        $total_cart = 0;
        $total_checkout = 0;

        foreach($carts as $cart){
            $total_cart += ($cart->barang->price * $cart->jumlah);
        }

        foreach($checkouts as $checkout){
            $total_checkout += ($checkout->barang->price * $checkout->jumlah);
        }

        // dd($checkouts);

        return view("transaksi", [
            "barangs" => $barangs,
            "carts" => $carts,
            "checkouts" => $checkouts,
            "total_cart" => $total_cart,
            "total_checkout" => $total_checkout,
            "saldo" => $saldo
        ]);
    })->name("transaksi");
    
    Route::get('/add', function () {
        // Matches The "/admin/users" URL
    });

    Route::post('/create', function (Request $request) {
        if($request->type == 1){
            $invoice_id = "SAL_" . Auth::user()->id . now()->timestamp;

            Transaksi::create([
                "user_id" => Auth::user()->id,
                "jumlah" => $request->jumlah,
                "invoice_id" => $invoice_id,
                "type" => $request->type,
                "status" => 2
            ]);

            return redirect()->back()->with("status", "Top Up Saldo Sedang Diproses");
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

Route::prefix('menu')->group(function () {
    Route::get("/", function(){
        $barangs = Barang::all();

        return view("menu", [
            "barangs" => $barangs
        ]);
    })->name("menu");

    Route::post("/add", function(Request $request){
        Barang::create($request->all());

        return redirect()->back()->with("status", "Berhasil Menambahkan Menu");
    })->name("menu.add");

    Route::put("/edit/{id}", function(Request $request, $id){
        Barang::find($id)->update($request->all());

        return redirect()->back()->with("status", "Berhasil Mengedit Menu");
    })->name("menu.edit");

    Route::get("/delete/{id}", function($id){
        Barang::find($id)->delete();

        return redirect()->back()->with("status", "Berhasil Menghapus Menu");
    })->name("menu.delete");
});

Route::prefix('data_user')->group(function () {
    Route::get("/", function(){
        $users = User::all();

        return view("data_user", [
            "users" => $users
        ]);
    })->name("data_user");

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
            Saldo::create([
                "user_id" => $user->id,
                "saldo" => 0
            ]);
        }

        return redirect()->back()->with("status", "Berhasil Menambahkan User");
    })->name("data_user.add");

    Route::put("/edit/{id}", function(Request $request, $id){
        if($request->password == null){
            User::find($id)->update([
                "name" => $request->name,
                "email" => $request->email,
                "role_id" => $request->role_id
            ]);

            return redirect()->back()->with("status", "Berhasil Mengedit User");
        }

        User::find($id)->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role_id" => $request->role_id
        ]);

        return redirect()->back()->with("status", "Berhasil Mengedit User");
    })->name("data_user.edit");

    Route::get("/delete/{id}", function($id){
        $user = User::find($id);

        Saldo::where("user_id", $user->id)->delete();

        $user->delete();

        return redirect()->back()->with("status", "Berhasil Menghapus User & Saldo");
    })->name("data_user.delete");
});

Route::prefix('data_transaksi')->group(function () {
    Route::get("/", function(){
        $details = Transaksi::where("type", 2)
                        ->get();

        $transaksis = Transaksi::where('type', 2)
                        ->groupBy('invoice_id')
                        ->get();

        return view("data_transaksi", [
            "transaksis" => $transaksis,
            "details" => $details,
        ]);
    })->name("data_transaksi");

    Route::post("/add", function(Request $request){
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role_id" => $request->role_id
        ]);

        if($user->role_id == 4){
            Saldo::create([
                "user_id" => $user->id,
                "saldo" => 0
            ]);
        }

        return redirect()->back()->with("status", "Berhasil Menambahkan User");
    })->name("data_transaksi.add");

    Route::put("/edit/{id}", function(Request $request, $id){
        if($request->password == null){
            User::find($id)->update([
                "name" => $request->name,
                "email" => $request->email,
                "role_id" => $request->role_id
            ]);

            return redirect()->back()->with("status", "Berhasil Mengedit User");
        }

        User::find($id)->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role_id" => $request->role_id
        ]);

        return redirect()->back()->with("status", "Berhasil Mengedit User");
    })->name("data_transaksi.edit");

    Route::get("/delete/{id}", function($id){
        $user = User::find($id);

        Saldo::where("user_id", $user->id)->delete();

        $user->delete();

        return redirect()->back()->with("status", "Berhasil Menghapus User & Saldo");
    })->name("data_transaksi.delete");
});
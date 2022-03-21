<?php

use App\Models\Balance;
use App\Models\Item;
use App\Models\Transaction;
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

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::post("/addUser", function () {
    });
    Route::prefix('item')->group(function () {
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

    Route::get("topup", function () {
        $saldo = Balance::where("user_id", Auth::user()->id)->first();

        return view("topup", [
            "saldo" => $saldo
        ]);
    })->name("topup");

    Route::get("topup/setuju/{transaction_id}", function ($transaction_id) {
        $transaction = Transaction::find($transaction_id);

        $saldo = Balance::where("user_id", $transaction->user_id)->first();

        Balance::where("user_id", $transaction->user_id)->update([
            "saldo" => $saldo->saldo + $transaction->quantity
        ]);

        $transaction->update([
            "status" => 3
        ]);

        return redirect()->back()->with("status", "Topup disetujui");
    })->name("topup.setuju");

    Route::get("topup/tolak/{transaction_id}", function ($transaction_id) {
        $transaction = Transaction::find($transaction_id);

        $transaction->delete();

        return redirect()->back()->with("status", "Topup ditolak");
    })->name("topup.tolak");

    Route::get("jajan/setuju/{invoice_id}", function ($invoice_id) {
        $transactions = Transaction::where("invoice_id", $invoice_id);

        $total_data = 0;

        foreach ($transactions->get() as $transaction) {
            $total_data += ($transaction->quantity * $transaction->item->price);
        }

        $transactions->update([
            "status" => 4 //FINISHED
        ]);

        return redirect()->back()->with("status", "Jajan disetujui");
    })->name("jajan.setuju");

    Route::get("jajan/tolak/{invoice_id}", function ($invoice_id) {
        $transactions = Transaction::where("invoice_id", $invoice_id);

        $total_data = 0;

        foreach ($transactions->get() as $transaction) {
            $total_data += ($transaction->quantity * $transaction->item->price);
        }

        $saldo = Balance::where("user_id", $transactions->get()[0]->user_id)->first();

        $saldo->update([
            "saldo" => $saldo->saldo + $total_data
        ]);

        $transactions->update([
            "status" => 5 //REJECTED
        ]);

        return redirect()->back()->with("status", "Jajan ditolak");
    })->name("jajan.tolak");

    Route::post("addToCart/{id}", function (Request $request) {
        Transaction::create([
            "user_id" => Auth::user()->id,
            "item_id" => $request->item_id,
            "status" => 1,
            "quantity" => $request->quantity,
            "type" => 2
        ]);

        return redirect()->back()->with("status", "Berhasil menambahkan barang ke keranjang");
    })->name("addToCart");

    Route::get("checkout", function () {
        $invoice_id = "INV_" . Auth::user()->id . now()->timestamp;

        Transaction::where("user_id", Auth::user()->id)->where("type", 2)->where("status", 1)->update([
            "invoice_id" => $invoice_id,
            "status" => 2
        ]);

        return redirect()->back()->with("status", "Berhasil Checkout");
    })->name("checkout");

    Route::get("bayar", function () {
        $datas = Transaction::where("user_id", Auth::user()->id)
            ->where("type", 2)
            ->where("status", 2);

        $total_data = 0;

        foreach ($datas->get() as $data) {
            $total_data += ($data->item->price * $data->quantity);
        }

        $saldo = Balance::where("user_id", Auth::user()->id)->first();

        $saldo->update([
            "saldo" => $saldo->saldo - $total_data
        ]);

        $datas->update([
            "status" => 3
        ]);

        return redirect()->back()->with("status", "Berhasil Bayar. Menunggu konfirmasi Kantin");
    })->name("bayar");

    Route::prefix('transaction')->group(function () {
        Route::get('/', function () {
            $items = Item::all();
            $carts = Transaction::where("user_id", Auth::user()->id)->where("status", 1)->where("type", 2)->get();
            $checkouts = Transaction::where("user_id", Auth::user()->id)->where("status", 2)->where("type", 2)->get();
            $saldo = Balance::where("user_id", Auth::user()->id)->first();

            $total_cart = 0;
            $total_checkout = 0;

            foreach ($carts as $cart) {
                $total_cart += ($cart->item->price * $cart->quantity);
            }

            foreach ($checkouts as $checkout) {
                $total_checkout += ($checkout->item->price * $checkout->quantity);
            }

            // dd($checkouts);

            return view("transaction", [
                "items" => $items,
                "carts" => $carts,
                "checkouts" => $checkouts,
                "total_cart" => $total_cart,
                "total_checkout" => $total_checkout,
                "saldo" => $saldo
            ]);
        })->name("transaction");

        Route::get('/add', function () {
            // Matches The "/admin/users" URL
        });

        Route::post('/create', function (Request $request) {
            if ($request->type == 1) {
                $invoice_id = "SAL_" . Auth::user()->id . now()->timestamp;

                Transaction::create([
                    "user_id" => Auth::user()->id,
                    "quantity" => $request->quantity,
                    "invoice_id" => $invoice_id,
                    "type" => $request->type,
                    "status" => 2
                ]);

                return redirect()->back()->with("status", "Top Up Saldo Sedang Diproses");
            }
        })->name("transaction.create");

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
        Route::get("/", function () {
            $items = Item::all();

            return view("menu", [
                "items" => $items
            ]);
        })->name("menu");

        Route::post("/add", function (Request $request) {
            Item::create($request->all());

            return redirect()->back()->with("status", "Berhasil Menambahkan Menu");
        })->name("menu.add");

        Route::put("/edit/{id}", function (Request $request, $id) {
            Item::find($id)->update($request->all());

            return redirect()->back()->with("status", "Berhasil Mengedit Menu");
        })->name("menu.edit");

        Route::get("/delete/{id}", function ($id) {
            Item::find($id)->delete();

            return redirect()->back()->with("status", "Berhasil Menghapus Menu");
        })->name("menu.delete");
    });

    Route::prefix('data_user')->group(function () {
        Route::get("/", function () {
            $users = User::all();

            return view("data_user", [
                "users" => $users
            ]);
        })->name("data_user");

        Route::post("/add", function (Request $request) {
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

            if ($user->role_id == 4) {
                Balance::create([
                    "user_id" => $user->id,
                    "saldo" => 0
                ]);
            }

            return redirect()->back()->with("status", "Berhasil Menambahkan User");
        })->name("data_user.add");

        Route::put("/edit/{id}", function (Request $request, $id) {
            if ($request->password == null) {
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

        Route::get("/delete/{id}", function ($id) {
            $user = User::find($id);

            Balance::where("user_id", $user->id)->delete();

            $user->delete();

            return redirect()->back()->with("status", "Berhasil Menghapus User & Saldo");
        })->name("data_user.delete");
    });

    Route::prefix('data_transaction')->group(function () {
        Route::get("/", function () {
            $details = Transaction::where("type", 2)
                ->get();

            $transactions = Transaction::where('type', 2)
                ->groupBy('invoice_id')
                ->get();

            return view("data_transaction", [
                "transactions" => $transactions,
                "details" => $details,
            ]);
        })->name("data_transaction");

        Route::post("/add", function (Request $request) {
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "role_id" => $request->role_id
            ]);

            if ($user->role_id == 4) {
                Balance::create([
                    "user_id" => $user->id,
                    "saldo" => 0
                ]);
            }

            return redirect()->back()->with("status", "Berhasil Menambahkan User");
        })->name("data_transaction.add");

        Route::put("/edit/{id}", function (Request $request, $id) {
            if ($request->password == null) {
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
        })->name("data_transaction.edit");

        Route::get("/delete/{id}", function ($id) {
            $user = User::find($id);

            Balance::where("user_id", $user->id)->delete();

            $user->delete();

            return redirect()->back()->with("status", "Berhasil Menghapus User & Saldo");
        })->name("data_transaction.delete");
    });
});

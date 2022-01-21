<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pengajuans = Transaksi::where("type", 1)
                        ->where("status", 2)
                        ->get();

        $pengajuan_jajans = Transaksi::where("type", 2)
        ->where("status", 2)
        ->get();

        return view('home', [
            "pengajuans" => $pengajuans,
            "pengajuan_jajans" => $pengajuan_jajans
        ]);
    }
}

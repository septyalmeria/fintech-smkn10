<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Role;
use App\Models\Saldo;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(["name" => "Administrator"]);
        $bank_mini = Role::create(["name" => "Bank Mini"]);
        $kantin = Role::create(["name" => "Kantin"]);
        $siswa = Role::create(["name" => "Siswa"]);

        User::create([
            "name" => "Mujahid",
            "email" => "mujahidrs@gmail.com",
            "password" => Hash::make("mujahidrs"),
            "role_id" => $admin->id
        ]);

        User::create([
            "name" => "Rui",
            "email" => "rui@gmail.com",
            "password" => Hash::make("rui"),
            "role_id" => $kantin->id
        ]);

        User::create([
            "name" => "Azizah",
            "email" => "azizah@gmail.com",
            "password" => Hash::make("azizah"),
            "role_id" => $bank_mini->id
        ]);

        $wahyu = User::create([
            "name" => "Wahyu",
            "email" => "wahyu@gmail.com",
            "password" => Hash::make("wahyu"),
            "role_id" => $siswa->id
        ]);

        $piscok = Barang::create([
            "name" => "Piscok",
            "price" => 2500,
            "stock" => 50,
            "desc" => "Pisang Cokelat"
        ]);

        $risol = Barang::create([
            "name" => "Risol",
            "price" => 3000,
            "stock" => 50,
            "desc" => "Risol Aja"
        ]);

        $burger = Barang::create([
            "name" => "Burger",
            "price" => 6000,
            "stock" => 50,
            "desc" => "Burger Daging Tipis"
        ]);

        $oasis = Barang::create([
            "name" => "Oasis",
            "price" => 2000,
            "stock" => 50,
            "desc" => "Minuman"
        ]);

        $teh_pucuk = Barang::create([
            "name" => "Teh Pucuk",
            "price" => 3500,
            "stock" => 50,
            "desc" => "Minuman teh"
        ]);

        Saldo::create([
            "user_id" => $wahyu->id,
            "saldo" => 50000
        ]);

        //Isi Saldo
        Transaksi::create([
            "user_id" => $wahyu->id,
            "barang_id" => null,
            "jumlah" => 50000,
            "invoice_id" => "SAL_001",
            "type" => 1,
            "status" => 3
        ]);

        //Belanja
        // Transaksi::create([
        //     "user_id" => $wahyu->id,
        //     "barang_id" => $burger->id,
        //     "jumlah" => 2,
        //     "invoice_id" => "INV_001",
        //     "type" => 2,
        //     "status" => 1
        // ]);

        // Transaksi::create([
        //     "user_id" => $wahyu->id,
        //     "barang_id" => $oasis->id,
        //     "jumlah" => 2,
        //     "invoice_id" => "INV_001",
        //     "type" => 2,
        //     "status" => 1
        // ]);
    }
}

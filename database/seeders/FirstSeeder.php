<?php

namespace Database\Seeders;

use App\Models\Balance;
use App\Models\Item;
use App\Models\Role;
use App\Models\Transaction;
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
        $customer = Role::create(["name" => "Siswa"]);

        User::create([
            "name" => "Fadiah Ahmad",
            "email" => "fadiah@gmail.com",
            "password" => Hash::make("fadiah"),
            "role_id" => $admin->id
        ]);

        User::create([
            "name" => "Bella Mustika",
            "email" => "bella@gmail.com",
            "password" => Hash::make("bella"),
            "role_id" => $kantin->id
        ]);

        User::create([
            "name" => "Keren Matilda",
            "email" => "keren@gmail.com",
            "password" => Hash::make("keren"),
            "role_id" => $bank_mini->id
        ]);

        $septy = User::create([
            "name" => "Septy Almeria",
            "email" => "septy@gmail.com",
            "password" => Hash::make("septy"),
            "role_id" => $customer->id
        ]);

        $burger = Item::create([
            "image" => "burger.png",
            "name" => "Burger",
            "price" => 6000,
            "stock" => 25,
            "desc" => "Roti dan Daging"
        ]);

        $risol = Item::create([
            "image" => "risol.png",
            "name" => "Risol",
            "price" => 12000,
            "stock" => 25,
            "desc" => "Risol Mayones"
        ]);

        $teh_botol = Item::create([
            "image" => "teh_botol.png",
            "name" => "Teh Botol",
            "price" => 5000,
            "stock" => 25,
            "desc" => "Teh Botol"
        ]);

        $aqua = Item::create([
            "image" => "aqua.png",
            "name" => "Aqua",
            "price" => 3000,
            "stock" => 25,
            "desc" => "Air Mineral"
        ]);

        Balance::create([
            "user_id" => $septy->id,
            "saldo" => 50000
        ]);

        //Isi Saldo
        Transaction::create([
            "user_id" => $septy->id,
            "item_id" => null,
            "quantity" => 50000,
            "invoice_id" => "SAL_001",
            "type" => 1,
            "status" => 3
        ]);

        //Belanja
        // Transaction::create([
        //     "user_id" => $septy->id,
        //     "item_id" => $burger->id,
        //     "quantity" => 2,
        //     "invoice_id" => "INV_001",
        //     "type" => 2,
        //     "status" => 1
        // ]);

        // Transaction::create([
        //     "user_id" => $septy->id,
        //     "item_id" => $aqua->id,
        //     "quantity" => 2,
        //     "invoice_id" => "INV_001",
        //     "type" => 2,
        //     "status" => 1
        // ]);
    }
}

@extends('layouts.app')

<?php
$page = "Jajan";
?>

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    Saldo : Rp. {{ $saldo->saldo }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Cart {{ count($carts) > 0 ? "#" . $carts[0]->invoice_id : ""}}</div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carts as $key => $cart)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $cart->item->name }}</td>
                                    <td>Rp. {{ $cart->item->price }}</td>
                                    <td>{{ $cart->quantity }}</td>
                                    <td>Rp. {{ $cart->item->price * $cart->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">Total Harga: Rp. {{ $total_cart }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route("checkout") }}" class="btn btn-primary">Checkout</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">Checkout {{ count($carts) > 0 ? "#" . $carts[0]->invoice_id : ""}}</div>

                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($checkouts as $key => $checkout)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $checkout->item->name }}</td>
                                    <td>Rp. {{ $checkout->item->price }}</td>
                                    <td>{{ $checkout->quantity }}</td>
                                    <td>Rp. {{ $checkout->item->price * $checkout->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">Total Harga: Rp. {{ $total_checkout }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route("bayar") }}" class="btn btn-primary">Bayar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col">
            <div class="card mt-3">
                <div class="card-header">Menu</div>

                <div class="card-body">
                    <div class="row">
                        @foreach ($items as $item)
                            <div class="col col-md-2">
                                <div class="card mb-3">
                                    <img height="160" src={{ asset('assets/images/' . $item->image) }} class="card-img-top" alt="not found" />
                                    <div class="card-body">
                                        <h4 class="card-title">{{ $item->name }}</h4>
                                        <h5 class="card-title">Rp. {{$item->price}}</h5>
                                        <p class="card-text">{{ $item->desc }}</p>
                                        <form method="POST" action="{{ route("addToCart", ["id" => $item->id]) }}">
                                            @csrf
                                            <input type="number" name="quantity" class="form-control" value="1">
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            <button class="btn btn-primary mt-3" type="submit">Tambah ke Keranjang</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@extends('layouts.app')

<?php
$page = "Top Up";
?>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Top Up') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        Saldo : Rp. {{ $saldo->saldo }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <form method="POST" action="{{ route("transaction.create") }}">
                                            @csrf
                                            <div class="form-group mb-2">
                                                <label class="mb-2">Jumlah</label>
                                                <input type="number" name="quantity" class="form-control" placeholder="Masukkan nominal">
                                                <input type="hidden" name="type" value="1">
                                            </div>
                                            <div class=" d-grid gap-2 col-6 mx-auto">
                                                <button class="btn btn-primary" type="submit">Top Up</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

<?php
$page = "Home";
?>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(Auth::user()->role_id === 4)
                    <ul>
                        <li><a href="{{ route("topup") }}">Top Up</a></li>
                        <li><a href="{{ route("transaksi") }}">Jajan</a></li>
                        <li><a href="">Riwayat Transaksi</a></li>
                    </ul>
                    @endif
                    @if(Auth::user()->role_id === 2)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuans as $key => $pengajuan)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $pengajuan->user->name }}</td>
                                        <td>{{ $pengajuan->jumlah }}</td>
                                        <td>
                                            <a href="{{ route('topup.setuju', ["transaksi_id" => $pengajuan->id]) }}" class="btn btn-success">
                                                Setuju
                                            </a>
                                            <a href="{{ route('topup.tolak', ["transaksi_id" => $pengajuan->id]) }}" class="btn btn-danger">
                                                Tolak
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    @if(Auth::user()->role_id === 3)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuan_jajans as $key => $pengajuan_jajan)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $pengajuan_jajan->user->name }}</td>
                                        <td>{{ $pengajuan_jajan->jumlah }}</td>
                                        <td>
                                            <a href="{{ route('jajan.setuju', ["invoice_id" => $pengajuan_jajan->invoice_id]) }}" class="btn btn-success">
                                                Setuju
                                            </a>
                                            <a href="{{ route('jajan.tolak', ["invoice_id" => $pengajuan_jajan->invoice_id]) }}" class="btn btn-danger">
                                                Tolak
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

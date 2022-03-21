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

                    @if(Auth::user()->role_id === 1)
                    <ul>
                        <li><a href="{{ route("data_user") }}">Data User</a></li>
                        <li><a href="{{ route("data_transaction") }}">Data Transaksi</a></li>
                        <li><a href="">Riwayat Transaksi</a></li>
                    </ul>
                    @endif
                    @if(Auth::user()->role_id === 4)
                    <ul>
                        <li><a href="{{ route("topup") }}">Top Up</a></li>
                        <li><a href="{{ route("transaction") }}">Jajan</a></li>
                        <li><a href="">Riwayat Transaksi</a></li>
                    </ul>
                    @endif
                    @if(Auth::user()->role_id === 2)
                        <table class="table table-bordered table-hover">
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
                                        <td>{{ $pengajuan->quantity }}</td>
                                        <td>
                                            <a href="{{ route('topup.setuju', ["transaction_id" => $pengajuan->id]) }}" class="btn btn-success">
                                                Setuju
                                            </a>
                                            <a href="{{ route('topup.tolak', ["transaction_id" => $pengajuan->id]) }}" class="btn btn-danger">
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
                                    {{-- <th>Nominal</th> --}}
                                    <th>Invoice ID</th>
                                    <th>Status</th>
                                    <th>Detail</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jajan_by_invoices as $key => $jajan_by_invoice)
                                    @if($jajan_by_invoice->status == 2 || $jajan_by_invoice->status == 3)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $jajan_by_invoice->user->name }}</td>
                                        <td>{{ $jajan_by_invoice->invoice_id }}</td>
                                        {{-- <td>{{ $jajan_by_invoice->quantity }}</td> --}}
                                        <td>{{ $jajan_by_invoice->status == 2 ? "Pending" : "Completed" }}</td>
                                        <td>
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detail-{{ $jajan_by_invoice->invoice_id }}">
                                                Detail
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="detail-{{ $jajan_by_invoice->invoice_id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Pesanan #{{ $jajan_by_invoice->invoice_id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Menu</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Harga</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $counter = 1;
                                                                    $total_harga = 0;
                                                                ?>
                                                                @foreach ($pengajuan_jajans as $pengajuan_jajan)
                                                                    @if($pengajuan_jajan->invoice_id == $jajan_by_invoice->invoice_id)
                                                                        <?php $total_harga += $pengajuan_jajan->quantity * $pengajuan_jajan->item->price; ?>
                                                                        <tr>
                                                                            <td>{{ $counter++ }}</td>
                                                                            <td>{{ $pengajuan_jajan->item->name }}</td>
                                                                            <td>{{ $pengajuan_jajan->quantity }}</td>
                                                                            <td>{{ $pengajuan_jajan->item->price }}</td>
                                                                            <td>{{ $pengajuan_jajan->quantity * $pengajuan_jajan->item->price }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        Total Harga = {{ $total_harga }}
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($jajan_by_invoice->status == 3)
                                            <a href="{{ route('jajan.setuju', ["invoice_id" => $jajan_by_invoice->invoice_id]) }}" class="btn btn-success">
                                                Setuju
                                            </a>
                                            <a href="{{ route('jajan.tolak', ["invoice_id" => $jajan_by_invoice->invoice_id]) }}" class="btn btn-danger">
                                                Tolak
                                            </a>
                                            @else
                                            Menunggu Pembayaran
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
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

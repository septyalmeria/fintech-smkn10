@extends('layouts.app')

<?php
$page = "Data Transaction";
?>

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            Data Transaksi
                        </div>
                        <div class="col d-flex justify-content-end">
                            {{-- <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambah">
                                Tambah transaction
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Tambah transaction</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route("data_transaction.add") }}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name">

                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email">

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Konfirmasi Password</label>
                                            <input type="password" class="form-control" name="password_confirmation">
                                        </div>
                                        <div class="form-group">
                                            <label>Role</label>
                                            <select class="form-select @error('role_id') is-invalid @enderror" name="role_id">
                                                <option value="">Pilih Opsi</option>
                                                <option value="1">Administrator</option>
                                                <option value="2">Bank Mini</option>
                                                <option value="3">Kantin</option>
                                                <option value="4">Siswa</option>
                                            </select>

                                            @error('role_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                    </form>
                                </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama User</th>
                                <th>Invoice ID</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $key => $transaction)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->invoice_id }}</td>
                                    <td>
                                      @if($transaction->status == 1)
                                        ON CART
                                      @endif
                                      @if($transaction->status == 2)
                                        PENDING
                                      @endif
                                      @if($transaction->status == 3)
                                        COMPLETED
                                      @endif
                                      @if($transaction->status == 4)
                                        FINISHED
                                      @endif
                                    </td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#detail-{{ $transaction->invoice_id }}">
                                            Detail
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="detail-{{ $transaction->invoice_id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Detail Transaksi #{{ $transaction->invoice_id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Nama User: {{ $transaction->user->name }} <br/>
                                                    Status:
                                                    @if($transaction->status == 1)
                                                      ON CART
                                                    @endif
                                                    @if($transaction->status == 2)
                                                      PENDING
                                                    @endif
                                                    @if($transaction->status == 3)
                                                      COMPLETED
                                                    @endif
                                                    @if($transaction->status == 4)
                                                      FINISHED
                                                    @endif
                                                    <table class="table table-bordered table-hover">
                                                      <thead>
                                                        <tr>
                                                          <th>Nama Pesanan</th>
                                                          <th>Jumlah</th>
                                                          <th>Harga</th>
                                                          <th>Total</th>
                                                        </tr>
                                                      </thead>
                                                      <tbody>
                                                        <?php $total_harga = 0; ?>
                                                        @foreach ($details as $detail)
                                                          @if($detail->invoice_id == $transaction->invoice_id)
                                                            <?php $total_harga += $detail->quantity * $detail->item->price; ?>
                                                            <tr>
                                                              <td>{{ $detail->item->name }}</td>
                                                              <td>{{ $detail->quantity }}</td>
                                                              <td>{{ $detail->item->price }}</td>
                                                              <td>{{ $detail->quantity * $detail->item->price }}</td>
                                                            </tr>
                                                          @endif
                                                        @endforeach
                                                      </tbody>
                                                    </table>
                                                    Total Harga: {{ $total_harga }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                            </div>
                                        </div>


                                        {{-- <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-{{ $transaction->id }}">
                                            Hapus
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="delete-{{ $transaction->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Hapus transaction {{ $transaction->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus user {{ $transaction->name }}?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                                    <a href="{{ route("data_transaction.delete", ["id" => $transaction->id]) }}" type="submit" class="btn btn-primary">Ya</a>
                                                </div>
                                            </div>
                                            </div>
                                        </div> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="footer">
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                          PRINT
                        </button>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

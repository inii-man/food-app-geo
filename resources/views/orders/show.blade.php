<x-app-layout>
    @if(session('success'))
        <div class="container py-4">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Detail Pesanan #{{ $order->id }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Pemesan:</strong> {{ $order->user->name }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p><strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                            <p><strong>Alamat Pengiriman:</strong> {{ $order->address }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1"><strong>Status:</strong></p>
                            
                            @if(Auth::user()->hasRole('merchant') || Auth::user()->hasRole('admin'))
                                {{-- Merchant/Admin can change status --}}
                                <form action="{{ route('merchant.orders.updateStatus', $order) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-lg" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Telah Dikirim</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </form>
                            @else
                                {{-- Customer only sees status badge --}}
                                @if ($order->status == 'pending')
                                    <span class="badge bg-warning text-dark fs-6">Menunggu Konfirmasi</span>
                                @elseif ($order->status == 'processing')
                                    <span class="badge bg-info text-dark fs-6">Sedang Diproses</span>
                                @elseif ($order->status == 'delivered')
                                    <span class="badge bg-success fs-6">Telah Dikirim</span>
                                @elseif ($order->status == 'cancelled')
                                    <span class="badge bg-danger fs-6">Dibatalkan</span>
                                @endif
                            @endif
                            
                            <h4 class="mt-3"><strong>Total Harga:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    <hr>

                    <h4 class="mb-3">Item Pesanan:</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Makanan</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->foodMenu->name }}</td>
                                        <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Item:</th>
                                    <th>Rp{{ number_format($order->total_price, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
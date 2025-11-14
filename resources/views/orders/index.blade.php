<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                {{ __('Kelola Pesanan - Semua Pesanan') }}
            @else
                {{ __('Daftar Pesanan') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">
                            @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                                Semua Pesanan
                            @else
                                Daftar Pesanan Saya
                            @endif
                        </h3>
                        @if(Auth::user()->isCustomer())
                            <a href="{{ route('merchants.map') }}" class="btn btn-primary">
                                <i class="bi bi-map"></i> Pesan dari Merchant
                            </a>
                        @endif
                    </div>

                    @if($orders->isEmpty())
                        <div class="alert alert-info text-center">
                            @if(Auth::user()->isCustomer())
                                <p class="mb-0">Anda belum memiliki pesanan. <a href="{{ route('merchants.map') }}" class="alert-link">Lihat merchant dan buat pesanan</a>!</p>
                            @else
                                <p class="mb-0">Belum ada pesanan.</p>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 8%">No. Pesanan</th>
                                        @if(Auth::user()->isCustomer())
                                            <th style="width: 15%">Merchant</th>
                                        @endif
                                        @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                                            <th style="width: 12%">Pelanggan</th>
                                        @endif
                                        <th style="width: 12%">Tanggal</th>
                                        <th style="width: 22%">Alamat Pengiriman</th>
                                        <th style="width: 12%">Total Harga</th>
                                        <th style="width: {{ (Auth::user()->isMerchant() || Auth::user()->isAdmin()) ? '18%' : '15%' }}">Status</th>
                                        <th style="width: 8%">Item</th>
                                        <th style="width: 8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="align-middle">
                                                <strong>#{{ $order->id }}</strong>
                                            </td>
                                            @if(Auth::user()->isCustomer())
                                                <td class="align-middle">
                                                    <strong>{{ $order->merchant->name }}</strong><br>
                                                    <small class="text-muted">📍 {{ Str::limit($order->merchant->address, 30) }}</small>
                                                </td>
                                            @endif
                                            @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                                                <td class="align-middle">
                                                    {{ $order->user->name }}<br>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </td>
                                            @endif
                                            <td class="align-middle">
                                                {{ $order->created_at->format('d M Y') }}<br>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                {{ Str::limit($order->address, 40) }}
                                            </td>
                                            <td class="align-middle">
                                                <strong>Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>
                                            </td>
                                            <td class="align-middle">
                                                @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                                                    <form action="{{ route('merchant.orders.updateStatus', $order) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Dikirim</option>
                                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                                        </select>
                                                    </form>
                                                @else
                                                    @if($order->status == 'pending')
                                                        <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                                    @elseif($order->status == 'processing')
                                                        <span class="badge bg-info text-dark">Sedang Diproses</span>
                                                    @elseif($order->status == 'delivered')
                                                        <span class="badge bg-success">Telah Dikirim</span>
                                                    @elseif($order->status == 'cancelled')
                                                        <span class="badge bg-danger">Dibatalkan</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge bg-secondary">{{ $order->orderItems->count() }} item</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-light border">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Total Pesanan:</strong> {{ $orders->count() }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Pending:</strong> {{ $orders->where('status', 'pending')->count() }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Selesai:</strong> {{ $orders->where('status', 'delivered')->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

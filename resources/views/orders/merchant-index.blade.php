<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Pesanan - Merchant') }}
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
                        <h3 class="mb-0">Semua Pesanan</h3>
                        <div>
                            <span class="badge bg-secondary">Total: {{ $orders->count() }} pesanan</span>
                        </div>
                    </div>

                    @if($orders->isEmpty())
                        <div class="alert alert-info text-center">
                            <p class="mb-0">Belum ada pesanan masuk.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 8%">No. Order</th>
                                        <th style="width: 15%">Pelanggan</th>
                                        <th style="width: 12%">Tanggal</th>
                                        <th style="width: 20%">Alamat</th>
                                        <th style="width: 12%">Total</th>
                                        <th style="width: 8%">Item</th>
                                        <th style="width: 15%">Status</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="align-middle">
                                                <strong>#{{ $order->id }}</strong>
                                            </td>
                                            <td class="align-middle">
                                                {{ $order->user->name }}<br>
                                                <small class="text-muted">{{ $order->user->email }}</small>
                                            </td>
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
                                            <td class="align-middle text-center">
                                                <span class="badge bg-secondary">{{ $order->orderItems->count() }} item</span>
                                            </td>
                                            <td class="align-middle">
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
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                                    <i class="bi bi-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal Detail Order -->
                                        <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">Detail Pesanan #{{ $order->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <p><strong>Pelanggan:</strong> {{ $order->user->name }}</p>
                                                                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                                                                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Alamat Pengiriman:</strong><br>{{ $order->address }}</p>
                                                                <p><strong>Status:</strong>
                                                                    @if($order->status == 'pending')
                                                                        <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                                                    @elseif($order->status == 'processing')
                                                                        <span class="badge bg-info text-dark">Sedang Diproses</span>
                                                                    @elseif($order->status == 'delivered')
                                                                        <span class="badge bg-success">Telah Dikirim</span>
                                                                    @elseif($order->status == 'cancelled')
                                                                        <span class="badge bg-danger">Dibatalkan</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <h6 class="mb-3">Item Pesanan:</h6>
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Nama Makanan</th>
                                                                    <th>Harga</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($order->orderItems as $index => $item)
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
                                                                    <th colspan="4" class="text-end">Total:</th>
                                                                    <th>Rp{{ number_format($order->total_price, 0, ',', '.') }}</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-light border">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Total Pesanan:</strong> {{ $orders->count() }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Pending:</strong> 
                                        <span class="badge bg-warning text-dark">{{ $orders->where('status', 'pending')->count() }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Processing:</strong> 
                                        <span class="badge bg-info text-dark">{{ $orders->where('status', 'processing')->count() }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Delivered:</strong> 
                                        <span class="badge bg-success">{{ $orders->where('status', 'delivered')->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (for modal functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>

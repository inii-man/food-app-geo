<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Merchant') }}
            </h2>
            <div>
                <a href="{{ route('merchants.map') }}" class="btn btn-info btn-sm me-2">
                    <i class="bi bi-map"></i> Lihat Peta
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('merchants.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Tambah Merchant
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Location Search -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Cari Merchant Terdekat</h5>
                            <button onclick="findNearbyMerchants()" class="btn btn-success">
                                <i class="bi bi-geo-alt-fill"></i> Gunakan Lokasi Saya
                            </button>
                            <div id="location-status" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="row" id="merchants-list">
                        @forelse($merchants as $merchant)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($merchant->image)
                                        <img src="{{ asset('storage/' . $merchant->image) }}" class="card-img-top" alt="{{ $merchant->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="bi bi-shop" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $merchant->name }}</h5>
                                        <p class="card-text text-muted small">{{ Str::limit($merchant->description, 100) }}</p>
                                        <p class="card-text">
                                            <i class="bi bi-geo-alt"></i> {{ $merchant->address }}<br>
                                            <i class="bi bi-telephone"></i> {{ $merchant->phone ?? 'N/A' }}<br>
                                            @if($merchant->opening_time && $merchant->closing_time)
                                                <i class="bi bi-clock"></i> {{ $merchant->opening_time }} - {{ $merchant->closing_time }}
                                            @endif
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge {{ $merchant->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $merchant->is_active ? 'Buka' : 'Tutup' }}
                                            </span>
                                            <span class="text-muted small">{{ $merchant->foodMenus->count() }} Menu</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('merchants.show', $merchant) }}" class="btn btn-sm btn-primary flex-fill">Detail</a>
                                            @if(auth()->user()->isAdmin())
                                                <a href="{{ route('merchants.edit', $merchant) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('merchants.destroy', $merchant) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus merchant ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">Belum ada merchant yang terdaftar.</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $merchants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function findNearbyMerchants() {
            const statusDiv = document.getElementById('location-status');
            statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Mendapatkan lokasi...';

            if (!navigator.geolocation) {
                statusDiv.innerHTML = '<div class="alert alert-danger">Browser Anda tidak mendukung Geolocation</div>';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    statusDiv.innerHTML = '<div class="alert alert-info">Lokasi ditemukan! Mencari merchant terdekat...</div>';

                    // Redirect to nearby page with coordinates
                    window.location.href = `{{ route('merchants.nearby') }}?latitude=${lat}&longitude=${lng}`;
                },
                function(error) {
                    let errorMsg = 'Tidak dapat mendapatkan lokasi Anda.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = 'Anda menolak permintaan lokasi.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            errorMsg = 'Permintaan lokasi timeout.';
                            break;
                    }
                    statusDiv.innerHTML = `<div class="alert alert-danger">${errorMsg}</div>`;
                }
            );
        }
    </script>
</x-app-layout>

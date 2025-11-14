<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $merchant->name }}
            </h2>
            <a href="{{ route('merchants.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Merchant Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <div class="row">
                        <div class="col-md-4">
                            @if($merchant->image)
                                <img src="{{ asset('storage/' . $merchant->image) }}" class="img-fluid rounded" alt="{{ $merchant->name }}">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                                    <i class="bi bi-shop" style="font-size: 5rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3>{{ $merchant->name }}</h3>
                            <p class="text-muted">{{ $merchant->description }}</p>
                            
                            <div class="mb-3">
                                <h5>Informasi Kontak</h5>
                                <p class="mb-1">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> {{ $merchant->address }}
                                </p>
                                @if($merchant->phone)
                                    <p class="mb-1">
                                        <i class="bi bi-telephone-fill text-primary"></i> {{ $merchant->phone }}
                                    </p>
                                @endif
                                @if($merchant->opening_time && $merchant->closing_time)
                                    <p class="mb-1">
                                        <i class="bi bi-clock-fill text-success"></i> 
                                        Jam Operasional: {{ $merchant->opening_time }} - {{ $merchant->closing_time }}
                                    </p>
                                @endif
                                <p>
                                    <span class="badge {{ $merchant->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $merchant->is_active ? 'Buka' : 'Tutup' }}
                                    </span>
                                </p>
                            </div>

                            @if($merchant->latitude && $merchant->longitude)
                                <div id="merchant-map" style="height: 250px; width: 100%; border-radius: 8px;"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Food Menu -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>Menu Makanan</h4>
                        <div class="d-flex gap-2 align-items-center">
                            @if(auth()->user()->isMerchant() && auth()->user()->merchant_id == $merchant->id)
                                <a href="{{ route('foodmenu.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Tambah Menu
                                </a>
                            @endif
                            @if(auth()->user()->isCustomer())
                                <button type="button" class="btn btn-success btn-sm position-relative" onclick="showCart()">
                                    <i class="bi bi-cart3"></i> Keranjang
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-badge" style="display: none;">
                                        0
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        @forelse($merchant->foodMenus as $menu)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="bi bi-egg-fried text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $menu->name }}</h5>
                                        <p class="card-text text-muted small">{{ Str::limit($menu->description, 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="h5 text-success mb-0">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                            @if($menu->is_available)
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary">Habis</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($menu->is_available && auth()->user()->isCustomer())
                                        <div class="card-footer bg-white">
                                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})">
                                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Belum ada menu yang tersedia di merchant ini.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">
                            <i class="bi bi-cart-check"></i> Keranjang Belanja
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="merchant_id" value="{{ $merchant->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Merchant</label>
                            <input type="text" class="form-control" value="{{ $merchant->name }}" disabled>
                        </div>

                        <!-- Cart Items -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Menu yang Dipesan</label>
                            <div id="cart-items-container" class="border rounded p-3">
                                <p class="text-muted">Keranjang kosong</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="delivery_address" class="form-label fw-bold">Alamat Pengiriman</label>
                            <textarea class="form-control" name="delivery_address" id="delivery_address" rows="2" required>{{ auth()->user()->address }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi Pengiriman</label>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <input type="number" step="any" class="form-control" name="delivery_latitude" id="delivery_latitude" placeholder="Latitude" required value="{{ auth()->user()->latitude }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" step="any" class="form-control" name="delivery_longitude" id="delivery_longitude" placeholder="Longitude" required value="{{ auth()->user()->longitude }}">
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success w-100" onclick="getMyLocation()">
                                <i class="bi bi-geo-alt-fill"></i> Gunakan Lokasi Saya
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold">Catatan (Opsional)</label>
                            <textarea class="form-control" name="notes" id="notes" rows="2" placeholder="Contoh: Jangan pakai cabai, pakai sendok plastik"></textarea>
                        </div>

                        <div class="alert alert-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Harga:</strong>
                                <span id="total_price" class="h4 mb-0">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success" id="submitOrderBtn" disabled>
                            <i class="bi bi-check-circle"></i> Buat Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        let cartModal = null;

        // Add item to cart
        function addToCart(menuId, menuName, price) {
            const existingItem = cart.find(item => item.id === menuId);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: menuId,
                    name: menuName,
                    price: price,
                    quantity: 1
                });
            }
            
            updateCartBadge();
            showToast('success', menuName + ' ditambahkan ke keranjang');
        }

        // Remove item from cart
        function removeFromCart(menuId) {
            cart = cart.filter(item => item.id !== menuId);
            updateCartDisplay();
            updateCartBadge();
            showToast('info', 'Item dihapus dari keranjang');
        }

        // Update quantity
        function updateQuantity(menuId, delta) {
            const item = cart.find(item => item.id === menuId);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    removeFromCart(menuId);
                } else {
                    updateCartDisplay();
                }
            }
        }

        // Update cart badge
        function updateCartBadge() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-badge');
            
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Show cart modal
        function showCart() {
            if (cart.length === 0) {
                showToast('warning', 'Keranjang Anda masih kosong');
                return;
            }
            
            updateCartDisplay();
            
            if (!cartModal) {
                cartModal = new bootstrap.Modal(document.getElementById('orderModal'));
            }
            cartModal.show();
        }

        // Update cart display
        function updateCartDisplay() {
            const container = document.getElementById('cart-items-container');
            const submitBtn = document.getElementById('submitOrderBtn');
            
            if (cart.length === 0) {
                container.innerHTML = '<p class="text-muted mb-0">Keranjang kosong</p>';
                submitBtn.disabled = true;
                return;
            }

            let html = '<div class="list-group">';
            let total = 0;

            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.name}</h6>
                                <small class="text-muted">Rp ${formatNumber(item.price)} x ${item.quantity}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, -1)">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" disabled>${item.quantity}</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, 1)">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <strong class="text-success" style="min-width: 100px; text-align: right;">Rp ${formatNumber(subtotal)}</strong>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            
            // Add hidden inputs for order items
            html += '<div id="order-items-inputs">';
            cart.forEach(item => {
                html += `
                    <input type="hidden" name="items[${item.id}][food_menu_id]" value="${item.id}">
                    <input type="hidden" name="items[${item.id}][quantity]" value="${item.quantity}">
                `;
            });
            html += '</div>';

            container.innerHTML = html;
            document.getElementById('total_price').textContent = 'Rp ' + formatNumber(total);
            submitBtn.disabled = false;
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function getMyLocation() {
            if (!navigator.geolocation) {
                showToast('error', 'Browser Anda tidak mendukung Geolocation');
                return;
            }

            const latInput = document.getElementById('delivery_latitude');
            const lngInput = document.getElementById('delivery_longitude');
            
            latInput.value = 'Loading...';
            lngInput.value = 'Loading...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    showToast('success', 'Lokasi berhasil dideteksi');
                },
                function(error) {
                    showToast('error', 'Tidak dapat mendapatkan lokasi Anda');
                    latInput.value = '{{ auth()->user()->latitude ?? "" }}';
                    lngInput.value = '{{ auth()->user()->longitude ?? "" }}';
                }
            );
        }

        function showToast(type, message) {
            // Simple alert for now, you can replace with better toast notification
            const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ';
            console.log(`${icon} ${message}`);
            
            // You can use Bootstrap Toast here if you want
            const toastHtml = `
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                    <div class="toast show align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                ${icon} ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            `;
            
            const toastContainer = document.createElement('div');
            toastContainer.innerHTML = toastHtml;
            document.body.appendChild(toastContainer);
            
            setTimeout(() => {
                toastContainer.remove();
            }, 3000);
        }
    </script>

    @if($merchant->latitude && $merchant->longitude)
    <script>
        // Initialize map for merchant location
        const merchantMap = L.map('merchant-map').setView([{{ $merchant->latitude }}, {{ $merchant->longitude }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(merchantMap);

        const merchantIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        L.marker([{{ $merchant->latitude }}, {{ $merchant->longitude }}], {icon: merchantIcon})
            .addTo(merchantMap)
            .bindPopup('<strong>{{ $merchant->name }}</strong><br>{{ $merchant->address }}')
            .openPopup();
    </script>
    @endif
</x-app-layout>

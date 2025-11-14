# Quick Reference Guide - Multi-Merchant Geolocation System

## 🚀 Quick Start

### Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

### Test Accounts (All passwords: `password`)

```
Admin:     admin@example.com
Merchants: warungpadang@example.com, satesenayan@example.com, baksomalang@example.com,
           nasigoreng@example.com, ayamgeprek@example.com
Customers: customer1@example.com, customer2@example.com, customer3@example.com,
           customer4@example.com, customer5@example.com
```

See `TEST_CREDENTIALS.md` for complete details.

## 🛒 Customer Order Flow

### Shopping Cart System (Current)

```
1. Customer login → Dashboard
   ↓
2. Click "🗺️ Peta Merchant" in navbar
   ↓
3. View merchants on map OR click "Cari Merchant Terdekat"
   ↓
4. Click merchant marker → "Lihat Menu" or radius search result
   ↓
5. On merchant page: Browse menu, click "Tambah ke Keranjang"
   ↓
6. Cart counter updates in navbar: "🛒 Keranjang (X)"
   ↓
7. Click cart button → Modal opens with order summary
   ↓
8. Review items, adjust quantities, fill delivery details
   ↓
9. Click "Buat Pesanan" → Order created with multiple items
   ↓
10. Redirected to order detail page
```

### Deprecated Routes

```php
// OLD: routes/web.php
Route::get('/orders/create', [OrderController::class, 'create'])
    ->name('orders.create'); // ❌ DEPRECATED

// Use shopping cart flow instead (merchants.show → cart modal)
```

## 🛍️ Shopping Cart Implementation

### Frontend JavaScript (in merchants/show.blade.php)

```javascript
// Cart state
let cart = [];

// Add item to cart
function addToCart(menuId, menuName, price) {
    const existingItem = cart.find((item) => item.id === menuId);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: menuId,
            name: menuName,
            price: price,
            quantity: 1,
        });
    }

    updateCart();
    showToast("Item ditambahkan ke keranjang!");
}

// Remove item from cart
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

// Increase quantity
function increaseQuantity(index) {
    cart[index].quantity++;
    updateCart();
}

// Decrease quantity
function decreaseQuantity(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
        updateCart();
    }
}

// Update cart UI
function updateCart() {
    // Update navbar badge
    document.getElementById("cartCount").textContent = cart.length;
    document.getElementById("cartItemCount").textContent = cart.length;

    const cartItemsDiv = document.getElementById("cartItems");
    const totalPriceSpan = document.getElementById("totalPrice");

    let total = 0;
    cartItemsDiv.innerHTML = "";

    if (cart.length === 0) {
        cartItemsDiv.innerHTML =
            '<p class="text-center text-muted">Keranjang kosong</p>';
        totalPriceSpan.textContent = "0";
        return;
    }

    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        cartItemsDiv.innerHTML += `
            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                <div>
                    <strong>${item.name}</strong><br>
                    <small>Rp ${item.price.toLocaleString("id-ID")} × ${
            item.quantity
        }</small><br>
                    <strong>Rp ${itemTotal.toLocaleString("id-ID")}</strong>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                            onclick="decreaseQuantity(${index})">-</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                        ${item.quantity}
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                            onclick="increaseQuantity(${index})">+</button>
                    <button type="button" class="btn btn-sm btn-danger" 
                            onclick="removeFromCart(${index})">🗑️</button>
                </div>
            </div>
        `;
    });

    totalPriceSpan.textContent = total.toLocaleString("id-ID");
}

// Submit order
function submitOrder() {
    if (cart.length === 0) {
        alert("Keranjang masih kosong!");
        return;
    }

    const form = document.getElementById("checkoutForm");
    const cartInput = document.createElement("input");
    cartInput.type = "hidden";
    cartInput.name = "cart";
    cartInput.value = JSON.stringify(cart);
    form.appendChild(cartInput);

    form.submit();
}

// Toast notification
function showToast(message) {
    // Implementation depends on your toast library
    alert(message); // Simple fallback
}
```

### Cart Modal HTML (Bootstrap 5)

```blade
<button type="button" class="btn btn-primary position-relative"
        data-bs-toggle="modal" data-bs-target="#cartModal">
    🛒 Keranjang
    <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        0
    </span>
</button>

<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🛒 Keranjang Pesanan (<span id="cartItemCount">0</span> item)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems"></div>
                <hr>
                <h4>Total: Rp <span id="totalPrice">0</span></h4>

                <form id="checkoutForm" method="POST" action="{{ route('orders.store') }}">
                    @csrf
                    <input type="hidden" name="merchant_id" value="{{ $merchant->id }}">

                    <div class="mb-3">
                        <label class="form-label">Alamat Pengiriman</label>
                        <textarea name="delivery_address" class="form-control" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="delivery_lat" id="delivery_lat" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="delivery_lng" id="delivery_lng" class="form-control" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary mb-3" onclick="useMyLocation()">
                        📍 Gunakan Lokasi Saya
                    </button>

                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submitOrder()">Buat Pesanan</button>
            </div>
        </div>
    </div>
</div>
```

### Backend Controller (OrderController.php)

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'merchant_id' => 'required|exists:merchants,id',
        'cart' => 'required|json',
        'delivery_address' => 'required|string|max:500',
        'delivery_lat' => 'required|numeric|between:-90,90',
        'delivery_lng' => 'required|numeric|between:-180,180',
        'notes' => 'nullable|string|max:500',
    ]);

    $cart = json_decode($validated['cart'], true);

    if (empty($cart)) {
        return back()->with('error', 'Keranjang kosong!');
    }

    $merchant = Merchant::findOrFail($validated['merchant_id']);

    // Calculate total price
    $totalPrice = 0;
    foreach ($cart as $item) {
        $menu = FoodMenu::findOrFail($item['id']);
        $totalPrice += $menu->price * $item['quantity'];
    }

    // Calculate delivery distance
    $distance = $merchant->distanceFrom(
        $validated['delivery_lat'],
        $validated['delivery_lng']
    );

    // Create order
    $order = Order::create([
        'user_id' => auth()->id(),
        'merchant_id' => $validated['merchant_id'],
        'total_price' => $totalPrice,
        'status' => 'pending',
        'delivery_address' => $validated['delivery_address'],
        'delivery_lat' => $validated['delivery_lat'],
        'delivery_lng' => $validated['delivery_lng'],
        'distance_km' => round($distance, 2),
        'notes' => $validated['notes'],
    ]);

    // Create order items
    foreach ($cart as $item) {
        $menu = FoodMenu::findOrFail($item['id']);
        OrderItem::create([
            'order_id' => $order->id,
            'food_menu_id' => $item['id'],
            'quantity' => $item['quantity'],
            'price' => $menu->price,
        ]);
    }

    return redirect()->route('orders.show', $order)
        ->with('success', 'Pesanan berhasil dibuat!');
}
```

## 📍 Geolocation Features

### 1. Get User Location (JavaScript)

```javascript
navigator.geolocation.getCurrentPosition(
    function (position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        // Use coordinates
        document.getElementById("delivery_lat").value = lat;
        document.getElementById("delivery_lng").value = lng;
    },
    function (error) {
        // Handle error: PERMISSION_DENIED, POSITION_UNAVAILABLE, TIMEOUT
        console.error("Geolocation error:", error);
        alert("Tidak dapat mengakses lokasi. Silakan isi manual.");
    }
);
```

### 2. Initialize Leaflet Map

```javascript
// Create map
const map = L.map("map").setView([lat, lng], zoom);

// Add OpenStreetMap tiles
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 19,
}).addTo(map);

// Add marker
L.marker([lat, lng]).addTo(map).bindPopup("Info").openPopup();
```

### 3. Custom Marker Icons

```javascript
const customIcon = L.icon({
    iconUrl:
        "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
    shadowUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

L.marker([lat, lng], { icon: customIcon }).addTo(map);
```

## 📐 Haversine Formula

### PHP Implementation

```php
public function distanceFrom(float $latitude, float $longitude): float
{
    if (!$this->latitude || !$this->longitude) {
        return PHP_FLOAT_MAX;
    }

    $earthRadius = 6371; // km

    $latFrom = deg2rad($this->latitude);
    $lonFrom = deg2rad($this->longitude);
    $latTo = deg2rad($latitude);
    $lonTo = deg2rad($longitude);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos($latFrom) * cos($latTo) *
         sin($lonDelta / 2) * sin($lonDelta / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}
```

### SQL Query Scope

```php
public function scopeNearby($query, float $latitude, float $longitude, float $radius = 10)
{
    $haversine = "(6371 * acos(cos(radians(?))
                 * cos(radians(latitude))
                 * cos(radians(longitude) - radians(?))
                 + sin(radians(?))
                 * sin(radians(latitude))))";

    return $query
        ->selectRaw("*, {$haversine} AS distance", [$latitude, $longitude, $latitude])
        ->whereRaw("{$haversine} < ?", [$latitude, $longitude, $latitude, $radius])
        ->orderBy('distance');
}
```

### Usage

```php
// In controller
$merchants = Merchant::where('is_active', true)
    ->nearby($latitude, $longitude, $radius)
    ->get();

// Calculate distance
$distance = $merchant->distanceFrom($userLat, $userLng);
```

## 🎨 Modern UI Components

### Food Menu Create/Edit Form

Modern Bootstrap 5 design with image preview, price formatting, availability selector:

```blade
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">✨ Tambah Menu Baru</h5>

        <form method="POST" action="{{ route('foodmenu.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Name Input -->
            <div class="mb-4">
                <label class="form-label fw-bold">🍽️ Nama Menu</label>
                <input type="text" name="name" class="form-control form-control-lg"
                       placeholder="Contoh: Nasi Goreng Special" required>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="form-label fw-bold">📝 Deskripsi</label>
                <textarea name="description" class="form-control" rows="4"
                          placeholder="Deskripsikan menu Anda..."></textarea>
            </div>

            <!-- Price Input with Rp prefix -->
            <div class="mb-4">
                <label class="form-label fw-bold">💰 Harga</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="price" class="form-control"
                           placeholder="25000" min="0" required>
                </div>
            </div>

            <!-- Image Upload with Preview -->
            <div class="mb-4">
                <label class="form-label fw-bold">📷 Gambar Menu</label>
                <input type="file" name="image" class="form-control"
                       accept="image/*" onchange="previewImage(event)">
                <div class="mt-2">
                    <img id="imagePreview" src="" style="max-width: 200px; display: none;"
                         class="rounded border">
                </div>
            </div>

            <!-- Availability Selector -->
            <div class="mb-4">
                <label class="form-label fw-bold">✅ Ketersediaan</label>
                <select name="is_available" class="form-select form-select-lg">
                    <option value="1">Tersedia</option>
                    <option value="0">Tidak Tersedia</option>
                </select>
            </div>

            <!-- Tips Card -->
            <div class="alert alert-info">
                <h6 class="alert-heading">💡 Tips:</h6>
                <ul class="mb-0">
                    <li>Gunakan foto yang menarik dan berkualitas tinggi</li>
                    <li>Deskripsikan bahan dan rasa dengan detail</li>
                    <li>Pastikan harga sudah termasuk pajak</li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    ✨ Tambah Menu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const file = event.target.files[0];

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}
</script>
```

### Role-Based Navbar with Emoji Icons

```blade
<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    @if(auth()->user()->isCustomer())
        <!-- Customer Menu -->
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            🏠 {{ __('Dashboard') }}
        </x-nav-link>
        <x-nav-link :href="route('merchants.map')" :active="request()->routeIs('merchants.*')">
            🗺️ {{ __('Peta Merchant') }}
        </x-nav-link>
        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
            📦 {{ __('Pesanan Saya') }}
        </x-nav-link>

    @elseif(auth()->user()->isMerchant())
        <!-- Merchant Menu -->
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            🏠 {{ __('Dashboard') }}
        </x-nav-link>
        <x-nav-link :href="route('foodmenu.index')" :active="request()->routeIs('foodmenu.*')">
            🍳 {{ __('Menu Saya') }}
        </x-nav-link>
        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
            📦 {{ __('Kelola Pesanan') }}
        </x-nav-link>

    @elseif(auth()->user()->isAdmin())
        <!-- Admin Menu -->
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            🏠 {{ __('Dashboard') }}
        </x-nav-link>
        <x-nav-link :href="route('merchants.index')" :active="request()->routeIs('merchants.*')">
            🏪 {{ __('Merchants') }}
        </x-nav-link>
        <x-nav-link :href="route('foodmenu.index')" :active="request()->routeIs('foodmenu.*')">
            🍽️ {{ __('Food Menus') }}
        </x-nav-link>
        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
            📦 {{ __('Orders') }}
        </x-nav-link>
    @endif
</div>

<!-- User Dropdown with Role Badge -->
<button class="inline-flex items-center px-3 py-2 border...">
    <div>{{ Auth::user()->name }}</div>
    <span class="badge bg-primary ms-2">
        @if(auth()->user()->isAdmin()) 👑 Admin
        @elseif(auth()->user()->isMerchant()) 👨‍🍳 Merchant
        @else 👤 Customer
        @endif
    </span>
</button>
```

### Customer Profile with Address Editing

```blade
<div class="mb-4">
    <label class="form-label">📍 Alamat</label>
    <textarea id="address" name="address" class="form-control" rows="3"
              placeholder="Masukkan alamat lengkap Anda">{{ old('address', $user->address) }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">🌍 Latitude</label>
        <input type="text" id="latitude" name="latitude" class="form-control"
               value="{{ old('latitude', $user->latitude) }}" placeholder="-6.200000">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">🌍 Longitude</label>
        <input type="text" id="longitude" name="longitude" class="form-control"
               value="{{ old('longitude', $user->longitude) }}" placeholder="106.816666">
    </div>
</div>

<button type="button" class="btn btn-secondary mb-3" onclick="getMyLocation()">
    📍 Gunakan Lokasi Saya Saat Ini
</button>

<script>
function getMyLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            alert('Lokasi berhasil diambil!');
        }, function(error) {
            alert('Gagal mendapatkan lokasi: ' + error.message);
        });
    } else {
        alert('Geolocation tidak didukung oleh browser Anda');
    }
}
</script>
```

## 🗄️ Database Schema

### Merchants Table

```php
Schema::create('merchants', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('address');
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->string('phone')->nullable();
    $table->string('image')->nullable();
    $table->boolean('is_active')->default(true);
    $table->time('opening_time')->nullable();
    $table->time('closing_time')->nullable();
    $table->timestamps();
});
```

### Add Geolocation to Users

```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('merchant_id')->nullable()->constrained()->onDelete('cascade');
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->string('address')->nullable();
});
```

### Add Merchant to Food Menus

```php
Schema::table('food_menus', function (Blueprint $table) {
    $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
    $table->boolean('is_available')->default(true);
});
```

### Add Merchant to Orders with Delivery Coordinates

```php
Schema::table('orders', function (Blueprint $table) {
    $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
    $table->string('delivery_address', 500);
    $table->decimal('delivery_lat', 10, 8);
    $table->decimal('delivery_lng', 11, 8);
    $table->decimal('distance_km', 8, 2)->nullable();
    $table->text('notes')->nullable();
});
```

### Order Items Table (for Multiple Items per Order)

```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('food_menu_id')->constrained()->onDelete('cascade');
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

## 🎨 Blade Components

### Include Leaflet in Layout

```blade
{{-- In head --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

### Map Container

```blade
<div id="map" style="height: 500px; width: 100%;"></div>
```

### Merchant Card

```blade
<div class="card">
    @if($merchant->image)
        <img src="{{ asset('storage/' . $merchant->image) }}" class="card-img-top">
    @endif
    <div class="card-body">
        <h5>{{ $merchant->name }}</h5>
        <p><i class="bi bi-geo-alt"></i> {{ $merchant->address }}</p>
        <span class="badge {{ $merchant->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $merchant->is_active ? 'Buka' : 'Tutup' }}
        </span>
    </div>
</div>
```

## 🛣️ Routes

### Web Routes

```php
Route::middleware('auth')->group(function () {
    // Merchant routes
    Route::resource('merchants', MerchantController::class);
    Route::get('/merchants-map', [MerchantController::class, 'map'])->name('merchants.map');
    Route::get('/merchants-nearby', [MerchantController::class, 'nearby'])->name('merchants.nearby');
});
```

### API Routes

```php
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/merchants/nearby', [MerchantController::class, 'nearby']);
});
```

## 🎯 Controller Methods

### Nearby Search

```php
public function nearby(Request $request)
{
    $validated = $request->validate([
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'radius' => 'nullable|numeric|min:1|max:50',
    ]);

    $latitude = $validated['latitude'];
    $longitude = $validated['longitude'];
    $radius = $validated['radius'] ?? 10;

    $merchants = Merchant::where('is_active', true)
        ->nearby($latitude, $longitude, $radius)
        ->get();

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'merchants' => $merchants,
        ]);
    }

    return view('merchants.nearby', compact('merchants', 'latitude', 'longitude'));
}
```

### Map View

```php
public function map()
{
    $merchants = Merchant::where('is_active', true)
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    return view('merchants.map', compact('merchants'));
}
```

## 🔐 Authorization

### Role-Based Order Access

```php
// OrderController::index()
$user = Auth::user();

if ($user->isAdmin()) {
    // Admin: All orders from all merchants
    $orders = Order::with(['orderItems.foodMenu', 'user', 'merchant'])
        ->orderBy('created_at', 'desc')
        ->get();
} elseif ($user->isMerchant()) {
    // Merchant: Only orders for their merchant
    $orders = Order::where('merchant_id', $user->merchant_id)
        ->with(['orderItems.foodMenu', 'user', 'merchant'])
        ->orderBy('created_at', 'desc')
        ->get();
} else {
    // Customer: Only their own orders
    $orders = Order::where('user_id', Auth::id())
        ->with(['orderItems.foodMenu', 'user', 'merchant'])
        ->orderBy('created_at', 'desc')
        ->get();
}
```

### Check User Role

```php
// In controller
if (auth()->user()->isAdmin()) {
    // Admin only
}

if (auth()->user()->isMerchant()) {
    // Merchant only
}

if (auth()->user()->isCustomer()) {
    // Customer only
}
```

### Order Detail Authorization

```php
// OrderController::show()
public function show(Order $order)
{
    $user = Auth::user();

    // Merchant: Only view their merchant's orders
    if ($user->isMerchant()) {
        if ($order->merchant_id != $user->merchant_id) {
            abort(403, 'Unauthorized');
        }
    }
    // Customer: Only view their own orders
    elseif ($user->isCustomer()) {
        if ($order->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
    // Admin: Can view all orders

    return view('orders.show', compact('order'));
}
```

### Middleware

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});

Route::middleware(['auth', 'role:merchant'])->group(function () {
    // Merchant routes
});
```

## 📊 Common Queries

### Get All Active Merchants

```php
$merchants = Merchant::where('is_active', true)->get();
```

### Get Merchant with Menus

```php
$merchant = Merchant::with('foodMenus')->find($id);
```

### Get Orders for Merchant

```php
// For merchant users - only their orders
$orders = Order::where('merchant_id', auth()->user()->merchant_id)
    ->with(['orderItems.foodMenu', 'user'])
    ->latest()
    ->get();
```

### Get Customer Orders with Merchant Info

```php
// For customer users - their orders with merchant details
$orders = Order::where('user_id', auth()->id())
    ->with(['orderItems.foodMenu', 'merchant'])
    ->latest()
    ->get();

// In blade: {{ $order->merchant->name }}
```

### Get Nearby Merchants

```php
$nearby = Merchant::nearby($lat, $lng, 10)->get();
```

### Get Merchant's Orders

```php
$orders = Order::where('merchant_id', $merchantId)
    ->with('user', 'orderItems')
    ->latest()
    ->get();
```

## 🐛 Common Issues & Solutions

### Issue: Geolocation Permission Denied

```javascript
// Provide fallback
if (error.code === error.PERMISSION_DENIED) {
    alert("Please enable location access to find nearby restaurants");
    // Optionally: Show manual location input
}
```

### Issue: Map Not Showing

```javascript
// Ensure map container has explicit height
<div id="map" style="height: 500px;"></div>;

// Initialize after DOM loaded
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map("map").setView([lat, lng], 13);
});
```

### Issue: Markers Not Appearing

```javascript
// Make sure coordinates are valid
if (merchant.latitude && merchant.longitude) {
    L.marker([merchant.latitude, merchant.longitude]).addTo(map);
}
```

### Issue: HTTPS Required

```
Geolocation API requires HTTPS in production
Solution: Use SSL certificate or test on localhost
```

## 📱 Mobile Considerations

### Responsive Map

```css
#map {
    height: 400px;
    width: 100%;
}

@media (max-width: 768px) {
    #map {
        height: 300px;
    }
}
```

### Touch-Friendly Buttons

```blade
<button class="btn btn-lg btn-success w-100" onclick="findLocation()">
    <i class="bi bi-geo-alt-fill"></i> Use My Location
</button>
```

## 🔧 Development Commands

```bash
# Run migrations
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generate controller
php artisan make:controller MerchantController

# Generate model
php artisan make:model Merchant -m

# Run server
php artisan serve

# Watch assets
npm run dev
```

## 📝 Testing Coordinates

### Jakarta Locations (for testing)

```php
// Monas
'latitude' => -6.1753924,
'longitude' => 106.8271528,

// Bundaran HI
'latitude' => -6.1944175,
'longitude' => 106.8229388,

// Senayan
'latitude' => -6.2295158,
'longitude' => 106.8006552,
```

## 🎓 Learning Resources

-   Leaflet.js Docs: https://leafletjs.com/
-   OpenStreetMap: https://www.openstreetmap.org/
-   Geolocation API: https://developer.mozilla.org/en-US/docs/Web/API/Geolocation_API
-   Haversine Formula: https://en.wikipedia.org/wiki/Haversine_formula
-   Laravel Docs: https://laravel.com/docs

---

**Need Help?** Check DOCUMENTATION.md for detailed explanations!

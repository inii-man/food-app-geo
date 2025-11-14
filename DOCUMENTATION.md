# Multi-Merchant Food Ordering System dengan Geolocation

## 📋 Ringkasan Proyek

Aplikasi food ordering yang telah ditingkatkan dari sistem single-merchant menjadi **multi-merchant** dengan fitur **geolocation** untuk mencari restoran terdekat berdasarkan lokasi user.

## 🎯 Fitur Utama

### 1. **Multi-Merchant System**

-   ✅ Mendukung multiple merchants (restoran)
-   ✅ Setiap merchant memiliki profil lengkap (nama, deskripsi, alamat, kontak)
-   ✅ Setiap merchant dapat mengelola menu makanan mereka sendiri
-   ✅ Merchant hanya melihat pesanan untuk restoran mereka sendiri
-   ✅ Customer dapat melihat semua merchant dan memilih mana yang ingin dipesan
-   ✅ Customer melihat informasi merchant di setiap pesanan mereka

### 2. **Geolocation Features**

-   ✅ **Browser Geolocation API** - Mendapatkan lokasi user secara otomatis
-   ✅ **Leaflet.js Integration** - Peta interaktif dengan marker untuk merchant dan user
-   ✅ **Koordinat Penyimpanan** - Menyimpan latitude & longitude untuk merchant dan customer
-   ✅ **Haversine Formula** - Menghitung jarak antara dua koordinat
-   ✅ **Pencarian Terdekat** - Fitur mencari merchant dalam radius tertentu (default 10 km)

### 3. **Visualisasi Peta**

-   ✅ Peta dengan semua merchant
-   ✅ Marker berbeda warna untuk user (biru) dan merchant (merah/hijau)
-   ✅ Popup informasi merchant dengan detail dan jarak
-   ✅ Circle radius untuk menunjukkan area pencarian

## 🗄️ Database Schema

### Tabel Merchants

```sql
- id
- name (nama restoran)
- description (deskripsi)
- address (alamat lengkap)
- latitude (koordinat latitude)
- longitude (koordinat longitude)
- phone (nomor telepon)
- image (foto restoran)
- is_active (status buka/tutup)
- opening_time (jam buka)
- closing_time (jam tutup)
- timestamps
```

### Tabel Users (Updated)

```sql
- id
- name
- email
- password
- role (admin/merchant/customer)
- merchant_id (foreign key - untuk merchant staff)
- latitude (koordinat customer)
- longitude (koordinat customer)
- address (alamat customer)
- timestamps
```

### Tabel Food_Menus (Updated)

```sql
- id
- merchant_id (foreign key)
- name
- description
- price
- image
- is_available (status ketersediaan)
- timestamps
```

### Tabel Orders (Updated)

```sql
- id
- user_id (customer)
- merchant_id (restoran)
- total_price
- status
- delivery_address (alamat pengiriman)
- delivery_lat (koordinat latitude pengiriman)
- delivery_lng (koordinat longitude pengiriman)
- distance_km (jarak pengiriman)
- notes (catatan pesanan)
- timestamps
```

### Tabel Order_Items (New)

```sql
- id
- order_id (foreign key)
- food_menu_id (foreign key)
- quantity (jumlah item)
- price (harga saat pemesanan)
- timestamps
```

## 🔧 Teknologi yang Digunakan

### Backend

-   **Laravel 11** - PHP Framework
-   **MySQL** - Database
-   **Eloquent ORM** - Database queries dengan relationships

### Frontend

-   **Blade Templates** - Laravel templating engine
-   **Bootstrap 5** - CSS framework
-   **Leaflet.js 1.9.4** - Interactive maps library
-   **OpenStreetMap** - Map tiles provider

### JavaScript APIs

-   **Geolocation API** - Browser native API untuk lokasi
-   **Fetch API** - AJAX requests

## 📐 Haversine Formula Implementation

Rumus Haversine digunakan untuk menghitung jarak terpendek antara dua titik di permukaan bumi:

```php
public function distanceFrom(float $latitude, float $longitude): float
{
    $earthRadius = 6371; // Radius bumi dalam km

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

    return $earthRadius * $c; // Jarak dalam kilometer
}
```

### Implementasi di SQL Query

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

## 🚀 Fitur-Fitur Geolocation

### 1. Browser Geolocation API

```javascript
navigator.geolocation.getCurrentPosition(
    function (position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        // Gunakan koordinat untuk pencarian
    },
    function (error) {
        // Handle error
    }
);
```

### 2. Leaflet.js Map Integration

```javascript
// Inisialisasi peta
const map = L.map("map").setView([latitude, longitude], 13);

// Tambah tile layer dari OpenStreetMap
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 19,
}).addTo(map);

// Tambah marker
L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Anda").openPopup();
```

### 3. Pencarian Merchant Terdekat

-   User mengklik tombol "Gunakan Lokasi Saya"
-   Browser meminta permission untuk akses lokasi
-   Sistem mendapatkan koordinat user (latitude, longitude)
-   Backend mencari merchant dalam radius 10km menggunakan Haversine
-   Hasil ditampilkan dengan jarak terurut dari terdekat

### 4. Visualisasi Peta

-   **Blue Marker** - Lokasi user
-   **Red Marker** - Merchant (jauh)
-   **Green Marker** - Merchant terdekat (dalam radius)
-   **Circle** - Area pencarian radius

## 📱 User Flow

### Customer Flow:

1. Login sebagai customer
2. Klik "🗺️ Peta Merchant" di navbar
3. Pilih "Cari Merchant Terdekat" atau lihat peta semua merchant
4. Izinkan akses lokasi browser
5. Klik merchant yang diinginkan
6. Browse menu dan tambahkan item ke keranjang
7. Klik "🛒 Keranjang" untuk review pesanan
8. Isi alamat pengiriman dan koordinat
9. Submit pesanan
10. Lihat pesanan di "📦 Pesanan Saya" (dengan info merchant)

### Merchant Flow:

1. Login sebagai merchant
2. Dashboard menampilkan statistik merchant sendiri
3. Kelola menu makanan di "🍳 Menu Saya"
4. Lihat pesanan untuk merchant sendiri di "📦 Kelola Pesanan"
5. Update status pesanan (pending → processing → delivered)
6. Hanya melihat dan mengelola pesanan untuk restoran sendiri

### Admin Flow:

1. Login sebagai admin
2. Kelola semua merchant di "🏪 Merchants"
3. Tambah/edit/hapus merchant dengan koordinat lokasi
4. Lihat semua menu makanan dari semua merchant
5. Monitor semua pesanan dari semua merchant dan customer

## 🎨 Komponen UI

### 1. Merchants Index

-   Grid card semua merchant
-   Tombol "Cari Terdekat" dengan geolocation
-   Tombol "Lihat Peta"
-   Info merchant: nama, alamat, jam operasional, status

### 2. Merchants Map

-   Peta interaktif full-width
-   Semua merchant ditampilkan dengan marker
-   Kontrol radius pencarian
-   Tombol "Gunakan Lokasi Saya"
-   Marker berubah warna untuk merchant terdekat

### 3. Nearby Merchants

-   Hasil pencarian merchant terdekat
-   Peta dengan radius circle
-   List merchant dengan jarak
-   Sorted dari terdekat ke terjauh

### 4. Merchant Detail

-   Info lengkap merchant
-   Peta lokasi merchant
-   Daftar menu makanan
-   Tombol pesan (untuk customer)

## 🔐 Authorization & Roles

### Admin

-   Kelola semua merchant
-   Kelola semua menu makanan
-   Lihat semua pesanan dari semua merchant

### Merchant

-   Kelola menu makanan merchant sendiri
-   Lihat pesanan untuk merchant sendiri saja (filtered by merchant_id)
-   Update status pesanan untuk restoran sendiri
-   Tidak bisa melihat pesanan merchant lain

### Customer

-   Browse semua merchant
-   Cari merchant terdekat
-   Pesan makanan dengan shopping cart (multiple items)
-   Lihat riwayat pesanan dengan informasi merchant
-   Edit alamat dan lokasi di profile

## 📊 Performance Considerations

### Database Indexing

-   Index pada `latitude` dan `longitude` untuk query cepat
-   Index pada `merchant_id` untuk foreign key lookup
-   Index pada `is_active` untuk filtering

### Query Optimization

-   Eager loading untuk menghindari N+1 problem
-   Pagination pada list merchant
-   Caching hasil pencarian nearby (future enhancement)

### Map Performance

-   Lazy loading map tiles
-   Marker clustering untuk banyak merchant (future enhancement)
-   Debouncing pada search radius change

## 🔄 API Endpoints

### Web Routes

```php
GET  /merchants              - Daftar semua merchant
GET  /merchants/create       - Form tambah merchant (admin)
POST /merchants              - Simpan merchant baru
GET  /merchants/{id}         - Detail merchant
GET  /merchants/{id}/edit    - Form edit merchant
PUT  /merchants/{id}         - Update merchant
DELETE /merchants/{id}       - Hapus merchant
GET  /merchants-map          - Peta semua merchant
GET  /merchants-nearby       - Pencarian merchant terdekat
```

### AJAX Routes

```php
GET /api/merchants/nearby    - API pencarian nearby (JSON)
GET /api/merchants/all       - API semua merchant (JSON)
```

## 📈 Future Enhancements

1. **Real-time Tracking**

    - Live location tracking untuk delivery
    - WebSocket untuk real-time updates

2. **Advanced Filters**

    - Filter by cuisine type
    - Filter by rating
    - Filter by price range

3. **Route Planning**

    - Optimal delivery route calculation
    - Estimated delivery time

4. **Geocoding**

    - Reverse geocoding (coordinates to address)
    - Address autocomplete
    - Multiple delivery addresses per user

5. **Analytics**
    - Popular merchants by location
    - Heat map of orders
    - Distance-based delivery fees

## 🧪 Testing

### Test Scenarios

1. ✅ Merchant CRUD operations
2. ✅ Geolocation permission handling
3. ✅ Distance calculation accuracy
4. ✅ Map marker rendering
5. ✅ Nearby search with different radius
6. ✅ Authorization checks per role

## 📝 Installation & Setup

### Prerequisites

-   PHP 8.2+
-   Composer
-   MySQL 8.0+
-   Node.js & NPM

### Steps

```bash
# Clone repository
git clone <repository-url>
cd food-app-sederhana

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate:fresh --seed

# Storage link
php artisan storage:link

# Start server
php artisan serve
```

### Test Accounts

```
Admin:
Email: admin@example.com
Password: password

Merchant:
Email: merchant@example.com
Password: password

Customer:
Email: customer@example.com
Password: password
```

## 🎓 Pembelajaran

### Konsep yang Dipelajari

1. **Geolocation API** - Cara mendapatkan lokasi user
2. **Leaflet.js** - Implementasi interactive maps
3. **Haversine Formula** - Perhitungan jarak geografis
4. **Spatial Queries** - Query database berdasarkan koordinat
5. **Multi-tenancy Pattern** - Sistem multi-merchant
6. **Authorization** - Role-based access control

### Best Practices

1. ✅ Separation of concerns (MVC pattern)
2. ✅ DRY principle dalam model methods
3. ✅ Eloquent relationships untuk data integrity
4. ✅ Form validation untuk data quality
5. ✅ Error handling untuk geolocation
6. ✅ Responsive design untuk mobile compatibility

## 📞 Support

Untuk pertanyaan atau issues, silakan contact:

-   Email: support@foodapp.com
-   GitHub Issues: [repository-url]/issues

---

**Developed with ❤️ using Laravel & Leaflet.js**

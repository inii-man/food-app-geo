# Presentasi: Multi-Merchant Food Ordering System dengan Geolocation

## Slide 1: Title Slide

**Multi-Merchant Food Ordering System**
**dengan Fitur Geolocation**

Transformasi dari Single Merchant ke Multi-Merchant Platform
dengan Pencarian Restoran Terdekat

---

## Slide 2: Problem Statement

### Masalah Sebelumnya:

-   ❌ Sistem hanya mendukung 1 merchant
-   ❌ Customer tidak bisa memilih merchant
-   ❌ Tidak ada informasi lokasi dan jarak
-   ❌ Sulit mencari restoran terdekat

### Solusi:

-   ✅ Multi-merchant platform
-   ✅ Geolocation-based search
-   ✅ Interactive maps
-   ✅ Distance calculation

---

## Slide 3: Fitur Utama

### 1. Multi-Merchant System

-   Support unlimited merchants
-   Setiap merchant manage menu sendiri
-   Customer bebas pilih merchant

### 2. Geolocation Features

-   Browser Geolocation API
-   Leaflet.js for interactive maps
-   Automatic distance calculation
-   Nearby search (radius-based)

### 3. Visual Maps

-   OpenStreetMap integration
-   Custom markers (user, merchant)
-   Popup information
-   Search radius visualization

---

## Slide 4: Database Architecture

### Perubahan Database:

**Tabel Baru:**

-   `merchants` - Data restoran dengan koordinat

**Update Tabel Existing:**

-   `users` + merchant_id, latitude, longitude, address
-   `food_menus` + merchant_id, is_available
-   `orders` + merchant_id, delivery_lat/lng, distance_km

### Relationships:

```
Merchant → hasMany → FoodMenus
Merchant → hasMany → Orders
User → belongsTo → Merchant (untuk staff)
Order → belongsTo → Merchant
```

---

## Slide 5: Teknologi Stack

### Backend:

-   **Laravel 11** - PHP Framework
-   **MySQL** - Database dengan spatial data
-   **Eloquent ORM** - Relationships & queries

### Frontend:

-   **Blade Templates** - Server-side rendering
-   **Bootstrap 5** - Responsive UI
-   **Leaflet.js 1.9.4** - Interactive maps
-   **Geolocation API** - Browser location

### Map Provider:

-   **OpenStreetMap** - Free map tiles

---

## Slide 6: Geolocation API

### Browser Geolocation API

```javascript
navigator.geolocation.getCurrentPosition(function (position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    // Use coordinates
});
```

### Features:

-   ✅ Automatic location detection
-   ✅ Permission-based access
-   ✅ Accurate GPS coordinates
-   ✅ Error handling

---

## Slide 7: Leaflet.js Integration

### Map Initialization

```javascript
const map = L.map("map").setView([lat, lng], 13);

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);
```

### Features:

-   📍 Multiple marker types
-   🎨 Custom icons (colored markers)
-   💬 Interactive popups
-   📏 Circle radius visualization
-   🗺️ Pan & zoom controls

---

## Slide 8: Haversine Formula

### Perhitungan Jarak Geografis

**Formula:**

```
a = sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlon/2)
c = 2 × atan2(√a, √(1−a))
distance = R × c
```

**Implementasi PHP:**

```php
public function distanceFrom($lat, $lng): float
{
    $earthRadius = 6371; // km
    // ... haversine calculation
    return $earthRadius * $c;
}
```

**Hasil:** Jarak dalam kilometer dengan akurasi tinggi

---

## Slide 9: Nearby Search Algorithm

### Alur Pencarian:

1. **User Request** → Klik "Gunakan Lokasi Saya"
2. **Browser** → Minta permission lokasi
3. **Get Coordinates** → Latitude & Longitude
4. **Query Database** → Haversine dalam SQL
5. **Filter Results** → Merchant dalam radius
6. **Sort by Distance** → Terdekat → Terjauh
7. **Display Results** → Map + List view

### SQL Query dengan Haversine:

```sql
SELECT *,
    (6371 * acos(cos(radians(?)) * cos(radians(latitude))
    * cos(radians(longitude) - radians(?))
    + sin(radians(?)) * sin(radians(latitude)))) AS distance
FROM merchants
HAVING distance < ?
ORDER BY distance
```

---

## Slide 10: User Interface - Merchants Index

### Components:

-   **Grid Layout** - Card-based merchant display
-   **Search Button** - "Gunakan Lokasi Saya"
-   **Map Button** - "Lihat Peta"
-   **Merchant Info:**
    -   Nama & deskripsi
    -   Alamat & telepon
    -   Jam operasional
    -   Status (Buka/Tutup)
    -   Jumlah menu

---

## Slide 11: User Interface - Interactive Map

### Map Features:

-   **Full-width map** dengan OpenStreetMap
-   **Radius control** - Adjust search radius
-   **Location button** - Get user location
-   **Colored markers:**
    -   🔵 Blue = User location
    -   🔴 Red = Merchant (far)
    -   🟢 Green = Merchant (nearby)
-   **Info popups** - Click marker for details
-   **Circle overlay** - Show search radius

---

## Slide 12: User Interface - Nearby Results

### Display:

-   **User location** - Koordinat ditampilkan
-   **Interactive map** - Dengan radius circle
-   **Results grid:**
    -   Merchant cards
    -   Distance badge (km)
    -   Sort by distance
-   **Empty state** - Jika tidak ada hasil

---

## Slide 13: User Flow - Customer

### Journey:

1. **Login** sebagai customer
2. **Browse merchants** atau **Cari terdekat**
3. **Grant location permission** (jika nearby)
4. **View nearby merchants** dengan jarak
5. **Select merchant** → Lihat menu
6. **Order food** → Checkout
7. **Track order** → Merchant proses

### Benefits:

-   🎯 Menemukan restoran terdekat dengan mudah
-   📏 Tahu jarak sebelum memesan
-   🗺️ Visualisasi lokasi di peta
-   ⚡ Quick decision making

---

## Slide 14: User Flow - Merchant

### Journey:

1. **Login** sebagai merchant
2. **Manage menu** di merchant sendiri
3. **View orders** untuk merchant sendiri
4. **Update status** pesanan
5. **Track location** customer (future)

### Authorization:

-   ✅ Hanya bisa edit menu sendiri
-   ✅ Hanya lihat order sendiri
-   ✅ Tidak bisa akses merchant lain

---

## Slide 15: User Flow - Admin

### Capabilities:

1. **Manage merchants:**
    - Create/Read/Update/Delete
    - Set coordinates (lat/lng)
    - Set operating hours
2. **Monitor system:**
    - All merchants
    - All orders
    - All menus
3. **Map overview:**
    - See all merchant locations
    - Analytics (future)

---

## Slide 16: Authorization & Security

### Role-Based Access Control:

**Admin:**

-   ✅ Full access to all features
-   ✅ Manage all merchants & menus

**Merchant:**

-   ✅ Manage own menus only
-   ✅ View own orders only
-   ❌ Cannot access other merchants

**Customer:**

-   ✅ Browse all merchants
-   ✅ Search nearby
-   ✅ Place orders
-   ❌ Cannot manage menus

---

## Slide 17: Technical Implementation - Models

### Merchant Model:

```php
class Merchant extends Model
{
    // Relationships
    public function foodMenus()
    public function orders()
    public function users()

    // Geolocation
    public function distanceFrom($lat, $lng)
    public function scopeNearby($query, $lat, $lng, $radius)
}
```

### Key Features:

-   ✅ Haversine distance calculation
-   ✅ Nearby scope for queries
-   ✅ Eloquent relationships

---

## Slide 18: Technical Implementation - Controller

### MerchantController:

```php
// Standard CRUD
public function index()
public function create()
public function store()
public function show()
public function edit()
public function update()
public function destroy()

// Geolocation Features
public function nearby($lat, $lng, $radius)
public function map()
```

### Nearby Method:

-   Validate coordinates
-   Query with Haversine
-   Calculate distances
-   Return JSON or view

---

## Slide 19: Performance Optimization

### Database:

-   ✅ **Indexes** on lat/lng columns
-   ✅ **Eager loading** to avoid N+1
-   ✅ **Pagination** for large datasets
-   ✅ **Query caching** (future)

### Frontend:

-   ✅ **Lazy loading** map tiles
-   ✅ **Debouncing** on radius change
-   ✅ **Marker clustering** (future)
-   ✅ **CDN** for static assets

### Maps:

-   ✅ Tile caching by browser
-   ✅ Optimize marker count
-   ✅ Conditional popup loading

---

## Slide 20: Data Seeding

### Sample Data:

```php
MerchantSeeder:
- 5 merchants di Jakarta
- Koordinat nyata (lat/lng)
- Jam operasional berbeda
- Status aktif

UserSeeder:
- Admin, Merchant, Customer
- Merchant linked to merchants
```

### Testing:

-   ✅ Nearby search works
-   ✅ Distance calculation accurate
-   ✅ Map markers display correctly
-   ✅ Authorization enforced

---

## Slide 21: Demo Flow

### Live Demo:

1. **Login as Customer**
2. **Click "Gunakan Lokasi Saya"**
3. **Grant permission** → Get coordinates
4. **View nearby merchants** with distances
5. **Open Map View** → See all markers
6. **Click merchant** → View menu
7. **Place order** → Success!

### Show:

-   Map interaction
-   Distance calculation
-   Nearby filtering
-   Responsive design

---

## Slide 22: Challenges & Solutions

### Challenges:

1. **Geolocation permission** → User may deny
2. **Coordinate accuracy** → GPS can be inaccurate
3. **Performance** → Many markers on map
4. **Database queries** → Haversine is expensive

### Solutions:

1. ✅ Fallback to manual location input
2. ✅ Show accuracy radius
3. ✅ Marker clustering (future)
4. ✅ Database indexing + caching

---

## Slide 23: Future Enhancements

### Phase 2:

-   🚀 **Real-time tracking** - Live delivery tracking
-   🎯 **Route optimization** - Optimal delivery route
-   🔍 **Advanced filters** - Cuisine, rating, price
-   💳 **Distance-based fees** - Auto calculate delivery cost

### Phase 3:

-   📊 **Analytics dashboard** - Heat maps, popular areas
-   🗺️ **Geocoding** - Address autocomplete
-   📱 **Mobile app** - Native iOS/Android
-   🤖 **AI recommendations** - ML-based suggestions

---

## Slide 24: Learning Outcomes

### Konsep yang Dikuasai:

1. ✅ **Geolocation API** - Browser location access
2. ✅ **Leaflet.js** - Interactive map library
3. ✅ **Haversine Formula** - Distance calculation
4. ✅ **Spatial Queries** - Geographic database queries
5. ✅ **Multi-tenancy** - Multiple merchant system
6. ✅ **RBAC** - Role-based authorization

### Skills Gained:

-   Frontend: JavaScript APIs, Map libraries
-   Backend: Spatial calculations, Query optimization
-   Database: Geographic data modeling
-   Full-stack: Complete feature implementation

---

## Slide 25: Architecture Diagram

```
┌─────────────┐
│   Browser   │
│  (Customer) │
└──────┬──────┘
       │
       │ Geolocation API
       ▼
┌─────────────────────────────┐
│    Laravel Application      │
│                             │
│  ┌──────────────────────┐  │
│  │ MerchantController   │  │
│  │  - nearby()          │  │
│  │  - map()             │  │
│  └──────────────────────┘  │
│           │                 │
│           ▼                 │
│  ┌──────────────────────┐  │
│  │   Merchant Model     │  │
│  │  - distanceFrom()    │  │
│  │  - scopeNearby()     │  │
│  └──────────────────────┘  │
│           │                 │
└───────────┼─────────────────┘
            │
            ▼
    ┌──────────────┐
    │    MySQL     │
    │  - merchants │
    │  - lat/lng   │
    └──────────────┘
            │
            ▼
    ┌──────────────┐
    │  Blade View  │
    │  + Leaflet   │
    │  + OSM Tiles │
    └──────────────┘
```

---

## Slide 26: Code Highlights

### Haversine in Model:

```php
public function distanceFrom(float $lat, float $lng): float
{
    $R = 6371; // Earth radius in km
    // Convert to radians
    // Calculate using Haversine
    return $R * $c;
}
```

### Nearby Scope:

```php
public function scopeNearby($query, $lat, $lng, $radius = 10)
{
    return $query->selectRaw("*, {$haversine} AS distance")
                 ->whereRaw("{$haversine} < ?", [..., $radius])
                 ->orderBy('distance');
}
```

---

## Slide 27: API Endpoints

### Web Routes:

-   `GET /merchants` - List all merchants
-   `GET /merchants/{id}` - Merchant detail
-   `GET /merchants-map` - Map view
-   `GET /merchants-nearby` - Nearby search

### AJAX API:

-   `GET /api/merchants/nearby?lat=&lng=&radius=`
    -   Returns: JSON with merchants + distances
-   `GET /api/merchants/all`
    -   Returns: All active merchants

---

## Slide 28: Testing & Validation

### Test Cases:

✅ User can grant location permission
✅ Haversine calculation is accurate
✅ Nearby search returns correct merchants
✅ Distance sorting works properly
✅ Map markers display at correct positions
✅ Authorization prevents unauthorized access
✅ Mobile responsive design

### Test Data:

-   5 merchants with real Jakarta coordinates
-   Test user at specific location
-   Verify distances manually

---

## Slide 29: Deployment Considerations

### Production Checklist:

-   ✅ HTTPS required for Geolocation API
-   ✅ Database indexes on lat/lng
-   ✅ Image optimization for merchant photos
-   ✅ Map tile caching
-   ✅ API rate limiting
-   ✅ Error logging for geolocation failures
-   ✅ Backup strategy for location data

### Monitoring:

-   Track geolocation success rate
-   Monitor map load times
-   Log distance calculation performance

---

## Slide 30: Conclusion

### Achievements:

✅ Transformed single to multi-merchant system
✅ Implemented geolocation features
✅ Integrated interactive maps with Leaflet.js
✅ Built nearby search with Haversine formula
✅ Created intuitive UI/UX
✅ Implemented proper authorization

### Impact:

-   📈 Better user experience
-   🎯 Find restaurants easily
-   📏 Know distance before ordering
-   🗺️ Visual location information
-   ⚡ Faster decision making

### Key Takeaway:

**Geolocation + Maps = Game Changer for Food Delivery!**

---

## Slide 31: Q&A

### Questions?

**Contact:**

-   Email: support@foodapp.com
-   GitHub: [repository-url]
-   Documentation: DOCUMENTATION.md

**Demo Account:**

-   Admin: admin@example.com / password
-   Merchant: merchant@example.com / password
-   Customer: customer@example.com / password

---

## Slide 32: Thank You!

**Multi-Merchant Food Ordering System**
**dengan Geolocation**

Developed with ❤️ using:

-   Laravel 11
-   Leaflet.js
-   OpenStreetMap
-   Bootstrap 5

**Live Demo:** [Your URL]
**Source Code:** [GitHub URL]
**Documentation:** [Docs URL]

---

# Catatan untuk Presentasi:

### Tips Presentasi:

1. **Slide 1-5:** Introduction & problem statement (5 mins)
2. **Slide 6-10:** Technical implementation (10 mins)
3. **Slide 11-15:** UI/UX & user flows (10 mins)
4. **Slide 16-20:** Architecture & optimization (10 mins)
5. **Slide 21:** LIVE DEMO (10 mins)
6. **Slide 22-30:** Challenges, future, conclusion (10 mins)
7. **Slide 31-32:** Q&A (5 mins)

### Demo Script:

1. Open app as customer
2. Click "Gunakan Lokasi Saya"
3. Grant permission → show coordinates
4. View nearby merchants with distances
5. Open map view → show markers and radius
6. Click merchant → view menu
7. Place order → show success

### Prepare:

-   Live site running
-   Test location permission beforehand
-   Backup screenshots if demo fails
-   Prepare for questions about Haversine
-   Explain security/authorization

# Multi-Merchant Login System - Penjelasan Lengkap

## 🔐 Konsep Login Per Merchant

Ya, **setiap merchant bisa memiliki akun login sendiri**. Sistem ini menggunakan pendekatan **User-Based Authentication** dengan **Merchant Relationship**.

## 📊 Struktur Data

### 1. Tabel `merchants`

Menyimpan data restoran/bisnis:

```sql
- id (Primary Key)
- name (nama restoran)
- description
- address
- latitude, longitude (lokasi)
- phone
- image
- is_active (status operasional)
- opening_time, closing_time
- timestamps
```

### 2. Tabel `users`

Menyimpan akun login:

```sql
- id (Primary Key)
- name (nama user)
- email (untuk login)
- password (hashed)
- role (admin/merchant/customer)
- merchant_id (Foreign Key → merchants.id)
- latitude, longitude (lokasi user)
- address
- timestamps
```

### 3. Relationship

```php
User belongsTo Merchant (via merchant_id)
Merchant hasMany Users (staff/owners)
```

## 🎯 Cara Kerja Login Per Merchant

### Skenario 1: Merchant Owner Baru Daftar

1. **Daftar di `/merchant/register`**

    - Isi data akun (nama, email, password)
    - Isi data restoran (nama, alamat, lokasi, dll)

2. **Sistem Membuat:**

    - 1 record di `merchants` table (data restoran)
    - 1 record di `users` table dengan:
        - `role = 'merchant'`
        - `merchant_id = [id restoran yang baru dibuat]`

3. **Auto Login**

    - User langsung login setelah registrasi
    - Redirect ke dashboard

4. **User Sekarang Bisa:**
    - Login dengan email & password
    - Manage menu makanan untuk restoran sendiri
    - Lihat & proses order untuk restoran sendiri
    - Update info restoran

### Skenario 2: Merchant Menambah Staff

Jika merchant ingin menambah staff/karyawan yang juga bisa akses sistem:

1. **Admin buat user baru** dengan:

    - `role = 'merchant'`
    - `merchant_id = [id restoran yang sama]`

2. **Staff bisa login** dan:
    - Manage menu restoran yang sama
    - Lihat order restoran yang sama
    - **Tidak bisa** akses merchant lain

### Skenario 3: Customer Login

-   Customer daftar dengan `role = 'customer'`
-   `merchant_id = NULL`
-   Bisa browse semua merchant
-   Bisa pesan dari merchant manapun

## 🔑 Flow Registrasi Merchant

```
┌─────────────────────────────┐
│  User Akses /merchant/register  │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│  Isi Form Registrasi:       │
│  - Data Akun (user)         │
│  - Data Restoran (merchant) │
│  - Lokasi (opsional)        │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│  Submit Form                │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│  Database Transaction:      │
│  1. Create Merchant         │
│  2. Create User (linked)    │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│  Auto Login                 │
│  Redirect to Dashboard      │
└─────────────────────────────┘
```

## 🔐 Authorization Logic

### Merchant Authorization di FoodMenuController

```php
// Index - Merchant hanya lihat menu sendiri
public function index()
{
    $user = auth()->user();

    if ($user->isMerchant() && $user->merchant_id) {
        // Hanya menu merchant sendiri
        $foodMenus = FoodMenu::where('merchant_id', $user->merchant_id)->get();
    } else {
        // Admin lihat semua
        $foodMenus = FoodMenu::with('merchant')->get();
    }

    return view('foodmenu.index', compact('foodMenus'));
}

// Edit - Cek kepemilikan
public function edit($id)
{
    $foodMenu = FoodMenu::findOrFail($id);
    $user = auth()->user();

    // Merchant hanya bisa edit menu sendiri
    if ($user->isMerchant() && $foodMenu->merchant_id != $user->merchant_id) {
        abort(403, 'Unauthorized action.');
    }

    return view('foodmenu.edit', compact('foodMenu'));
}
```

## 📝 Contoh Use Case

### Use Case 1: Warung Padang Sederhana

**Owner:** Pak Ahmad

-   Email: ahmad@warungpadang.com
-   Password: \*\*\*
-   Role: merchant
-   merchant_id: 1 (Warung Padang Sederhana)

**Capabilities:**

-   ✅ Login dengan email/password
-   ✅ Manage menu Warung Padang
-   ✅ Lihat order Warung Padang
-   ❌ Tidak bisa lihat/edit merchant lain

### Use Case 2: Sate Khas Senayan

**Owner:** Bu Siti

-   Email: siti@satesenayan.com
-   Password: \*\*\*
-   Role: merchant
-   merchant_id: 2 (Sate Khas Senayan)

**Capabilities:**

-   ✅ Login dengan email/password sendiri
-   ✅ Manage menu Sate Khas Senayan
-   ✅ Lihat order Sate Khas Senayan
-   ❌ Tidak bisa akses Warung Padang

### Use Case 3: Multiple Staff

**Warung Padang** bisa punya banyak user:

1. **Pak Ahmad** (Owner) - merchant_id: 1
2. **Rina** (Manager) - merchant_id: 1
3. **Budi** (Staff) - merchant_id: 1

Semua bisa login dengan akun masing-masing, tapi semuanya manage merchant yang sama (id: 1).

## 🚀 Cara Menggunakan

### 1. Merchant Baru Daftar

```
1. Buka: http://localhost:8000/merchant/register
2. Isi form registrasi
3. Klik "Daftar Sebagai Merchant"
4. Auto login → Dashboard
5. Mulai tambah menu makanan
```

### 2. Merchant Login (Sudah Punya Akun)

```
1. Buka: http://localhost:8000/login
2. Masukkan email & password
3. Klik "Log in"
4. Redirect ke dashboard
5. Akses menu management
```

### 3. Admin Tambah Merchant Baru

```
1. Login sebagai admin
2. Buka /merchants/create
3. Isi data restoran
4. Submit
5. Buat user untuk merchant tersebut (manual/via admin panel)
```

## 🛡️ Security & Authorization

### Middleware Protection

```php
Route::middleware(['auth', 'role:merchant'])->group(function () {
    Route::get('/foodmenu', [FoodMenuController::class, 'index']);
    Route::post('/foodmenu', [FoodMenuController::class, 'store']);
});
```

### Model-Level Checks

```php
// Di User model
public function isMerchant(): bool
{
    return $this->role === 'merchant';
}

public function merchant()
{
    return $this->belongsTo(Merchant::class);
}

// Di Controller
if (!auth()->user()->isMerchant()) {
    abort(403);
}

if ($menu->merchant_id !== auth()->user()->merchant_id) {
    abort(403);
}
```

## 📋 Checklist Fitur Login Merchant

-   ✅ Form registrasi merchant terpisah
-   ✅ Auto-create merchant & user dalam 1 transaksi
-   ✅ Link user ke merchant via merchant_id
-   ✅ Authorization per merchant
-   ✅ Merchant hanya lihat data sendiri
-   ✅ Multiple staff per merchant (support)
-   ✅ Role-based access control
-   ✅ Geolocation saat registrasi

## 🔄 Database Seeding Example

```php
// MerchantSeeder.php
$merchant = Merchant::create([
    'name' => 'Warung Padang Sederhana',
    'address' => 'Jl. Sudirman No. 123',
    'latitude' => -6.2088,
    'longitude' => 106.8456,
]);

// UserSeeder.php
User::create([
    'name' => 'Pak Ahmad',
    'email' => 'ahmad@warungpadang.com',
    'password' => Hash::make('password'),
    'role' => 'merchant',
    'merchant_id' => $merchant->id, // Link ke merchant
]);
```

## 🎯 Benefits

1. **Scalable** - Unlimited merchants bisa register
2. **Secure** - Setiap merchant hanya akses data sendiri
3. **Flexible** - 1 merchant bisa punya banyak staff
4. **Independent** - Setiap merchant manage bisnis sendiri
5. **Geolocation** - Lokasi merchant tersimpan untuk fitur nearby

## 🚨 Penting!

### Jangan Lupa:

1. **Validation** - Email harus unique
2. **Transaction** - Gunakan DB::transaction untuk create merchant + user
3. **Authorization** - Selalu cek merchant_id di controller
4. **Rollback** - Handle error dengan proper rollback
5. **Password** - Always hash passwords

### Security Best Practices:

```php
// ✅ Good
if ($user->merchant_id !== $foodMenu->merchant_id) {
    abort(403);
}

// ❌ Bad (tidak cek ownership)
$foodMenu = FoodMenu::find($id);
$foodMenu->update($request->all());
```

## 📞 Login Flow Summary

```
Merchant Register → Create Merchant + User → Auto Login → Dashboard
         ↓
    merchant_id stored in users table
         ↓
    Login dengan email/password → Check role & merchant_id
         ↓
    Authorization di setiap action berdasarkan merchant_id
         ↓
    Merchant hanya bisa CRUD data dengan merchant_id yang sama
```

---

**Kesimpulan:**
Ya, sistem sudah support **login per merchant**! Setiap merchant punya akun sendiri dan hanya bisa manage restoran/menu mereka sendiri. Admin bisa manage semua merchant. Customer bisa browse & order dari merchant manapun.

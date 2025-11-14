# UserSeeder Update Summary

## Changes Made

### Before

The `UserSeeder` created only 3 generic test users:

-   1 Admin User
-   1 Generic Merchant User (no `merchant_id` linkage)
-   1 Customer User

**Problem**: The merchant user had no connection to any actual merchant entity in the database.

### After

The `UserSeeder` now creates **11 comprehensive test accounts**:

-   **1 Admin User** - Full system access
-   **5 Merchant Users** - Each linked to a specific merchant entity
-   **5 Customer Users** - With different locations for geolocation testing

---

## New Test Accounts Structure

### 1. Admin Account (1)

```
Email: admin@example.com
Role: admin
```

### 2. Merchant Accounts (5)

Each merchant now has a dedicated user account properly linked via `merchant_id`:

| Merchant                        | Email                    | Merchant ID | Location                              |
| ------------------------------- | ------------------------ | ----------- | ------------------------------------- |
| Warung Padang Sederhana         | warungpadang@example.com | 1           | Jl. Sudirman No. 123, Jakarta Pusat   |
| Sate Khas Senayan               | satesenayan@example.com  | 2           | Jl. Kebon Sirih No. 45, Jakarta Pusat |
| Bakso Malang Cak Eko            | baksomalang@example.com  | 3           | Jl. Thamrin No. 67, Jakarta Pusat     |
| Nasi Goreng Kambing Kebon Sirih | nasigoreng@example.com   | 4           | Jl. Kebon Sirih Raya No. 89, Jakarta  |
| Ayam Geprek Bensu               | ayamgeprek@example.com   | 5           | Jl. Sabang No. 12, Jakarta Pusat      |

### 3. Customer Accounts (5)

Multiple customers with different locations for testing nearby search:

| Name           | Email                 | Location                          |
| -------------- | --------------------- | --------------------------------- |
| Budi Santoso   | customer1@example.com | Jl. Menteng Raya No. 10, Jakarta  |
| Siti Nurhaliza | customer2@example.com | Jl. Cikini Raya No. 25, Jakarta   |
| Ahmad Wijaya   | customer3@example.com | Jl. Salemba Raya No. 15, Jakarta  |
| Dewi Lestari   | customer4@example.com | Jl. Matraman Raya No. 30, Jakarta |
| Rudi Hartono   | customer5@example.com | Jl. Gatot Subroto No. 50, Jakarta |

---

## Technical Implementation

### Key Features Added

1. **Proper Merchant Linkage**

    ```php
    User::create([
        'merchant_id' => $account['merchant_id'],  // Links to merchants table
        'name' => $account['name'],
        'email' => $account['email'],
        'role' => 'merchant',
        // ... other fields
    ]);
    ```

2. **Geolocation Data for All Users**

    - Merchant users inherit their merchant's coordinates
    - Customer users have unique coordinates for distance testing
    - All coordinates are in Jakarta area for realistic testing

3. **Seeder Execution Order**
   Updated `DatabaseSeeder.php` to ensure proper order:
    ```php
    $this->call([
        MerchantSeeder::class,  // Creates merchants FIRST
        UserSeeder::class,       // Then creates users linked to merchants
    ]);
    ```

---

## Benefits

### 1. Multi-Tenant Testing

-   Each merchant can log in with their own credentials
-   Merchants see only their own menu items and orders
-   Proper isolation between merchants

### 2. Realistic Data

-   Each merchant has a unique email based on restaurant name
-   Real Jakarta addresses and coordinates
-   Multiple customers at different locations

### 3. Geolocation Testing

-   Test "Find Nearby" feature with customers at various distances
-   Each user has latitude/longitude for distance calculations
-   Haversine formula can be tested with real coordinate data

### 4. Authorization Testing

-   Verify merchant cannot access other merchant's data
-   Test customer ordering from different merchants
-   Admin can view all data

---

## Verification Results

After running `php artisan migrate:fresh --seed`:

```
✅ Total Users: 11
✅ Merchants: 5 (all linked to merchant entities)
✅ Customers: 5 (with unique locations)
✅ Admin: 1

Merchant User Linkages:
  - Warung Padang Admin → Merchant ID: 1 ✓
  - Sate Senayan Admin → Merchant ID: 2 ✓
  - Bakso Malang Admin → Merchant ID: 3 ✓
  - Nasi Goreng Admin → Merchant ID: 4 ✓
  - Ayam Geprek Admin → Merchant ID: 5 ✓
```

---

## Testing Instructions

### Test Merchant Isolation

1. Login as `warungpadang@example.com` (password: `password`)
2. Create a food menu item
3. Logout and login as `satesenayan@example.com`
4. Verify you cannot see Warung Padang's menu items

### Test Geolocation

1. Login as `customer1@example.com`
2. Go to Merchants → Find Nearby
3. Allow browser location access
4. See merchants sorted by distance from Menteng location

### Test Multi-Customer Orders

1. Login as different customer accounts
2. Place orders from various merchants
3. Verify order tracking and merchant order views

---

## Files Modified

1. **database/seeders/UserSeeder.php**

    - Added 5 merchant account definitions with `merchant_id`
    - Added 5 customer account definitions with coordinates
    - Proper address and location data for all users

2. **database/seeders/DatabaseSeeder.php**
    - Changed seeder order: `MerchantSeeder` → `UserSeeder`
    - Ensures merchants exist before creating linked users

---

## Default Password

All test accounts use the same password:

```
password
```

---

## Quick Reference

See `TEST_CREDENTIALS.md` for complete login details and testing scenarios.

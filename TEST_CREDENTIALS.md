# Test Credentials

This document contains all test login credentials for the multi-merchant food ordering system.

## Default Password

All accounts use the same password for testing:

```
password
```

---

## Admin Account

| Role  | Name       | Email             | Access Level       |
| ----- | ---------- | ----------------- | ------------------ |
| Admin | Admin User | admin@example.com | Full system access |

**Admin Capabilities:**

-   Manage all merchants
-   Manage all users
-   View all orders
-   System-wide configuration

---

## Merchant Accounts

Each merchant has their own dedicated login account linked to their restaurant.

### 1. Warung Padang Sederhana

| Field       | Value                               |
| ----------- | ----------------------------------- |
| Email       | warungpadang@example.com            |
| Merchant ID | 1                                   |
| Location    | Jl. Sudirman No. 123, Jakarta Pusat |
| Coordinates | -6.2088, 106.8456                   |
| Hours       | 08:00 - 22:00                       |

### 2. Sate Khas Senayan

| Field       | Value                                 |
| ----------- | ------------------------------------- |
| Email       | satesenayan@example.com               |
| Merchant ID | 2                                     |
| Location    | Jl. Kebon Sirih No. 45, Jakarta Pusat |
| Coordinates | -6.1951, 106.8231                     |
| Hours       | 10:00 - 23:00                         |

### 3. Bakso Malang Cak Eko

| Field       | Value                             |
| ----------- | --------------------------------- |
| Email       | baksomalang@example.com           |
| Merchant ID | 3                                 |
| Location    | Jl. Thamrin No. 67, Jakarta Pusat |
| Coordinates | -6.1944, 106.8229                 |
| Hours       | 09:00 - 21:00                     |

### 4. Nasi Goreng Kambing Kebon Sirih

| Field       | Value                                      |
| ----------- | ------------------------------------------ |
| Email       | nasigoreng@example.com                     |
| Merchant ID | 4                                          |
| Location    | Jl. Kebon Sirih Raya No. 89, Jakarta Pusat |
| Coordinates | -6.1875, 106.8304                          |
| Hours       | 17:00 - 02:00                              |

### 5. Ayam Geprek Bensu

| Field       | Value                            |
| ----------- | -------------------------------- |
| Email       | ayamgeprek@example.com           |
| Merchant ID | 5                                |
| Location    | Jl. Sabang No. 12, Jakarta Pusat |
| Coordinates | -6.1867, 106.8286                |
| Hours       | 10:00 - 22:00                    |

**Merchant Capabilities:**

-   Manage own food menu (CRUD)
-   View and manage orders for own restaurant
-   Update restaurant information
-   View restaurant statistics

---

## Customer Accounts

### 1. Budi Santoso

| Field       | Value                                  |
| ----------- | -------------------------------------- |
| Email       | customer1@example.com                  |
| Location    | Jl. Menteng Raya No. 10, Jakarta Pusat |
| Coordinates | -6.1950, 106.8300                      |

### 2. Siti Nurhaliza

| Field       | Value                                 |
| ----------- | ------------------------------------- |
| Email       | customer2@example.com                 |
| Location    | Jl. Cikini Raya No. 25, Jakarta Pusat |
| Coordinates | -6.1920, 106.8350                     |

### 3. Ahmad Wijaya

| Field       | Value                                  |
| ----------- | -------------------------------------- |
| Email       | customer3@example.com                  |
| Location    | Jl. Salemba Raya No. 15, Jakarta Pusat |
| Coordinates | -6.2000, 106.8400                      |

### 4. Dewi Lestari

| Field       | Value                                   |
| ----------- | --------------------------------------- |
| Email       | customer4@example.com                   |
| Location    | Jl. Matraman Raya No. 30, Jakarta Timur |
| Coordinates | -6.2100, 106.8500                       |

### 5. Rudi Hartono

| Field       | Value                                     |
| ----------- | ----------------------------------------- |
| Email       | customer5@example.com                     |
| Location    | Jl. Gatot Subroto No. 50, Jakarta Selatan |
| Coordinates | -6.2200, 106.8300                         |

**Customer Capabilities:**

-   Browse all merchants
-   Search nearby restaurants using geolocation
-   View restaurant menus
-   Place orders
-   Track order history
-   Update delivery location

---

## Quick Testing Scenarios

### Scenario 1: Merchant Login & Menu Management

1. Login as: `warungpadang@example.com`
2. Navigate to: Food Menu section
3. Create/edit menu items for Warung Padang Sederhana
4. Only items from this merchant will be visible

### Scenario 2: Customer Order Flow

1. Login as: `customer1@example.com`
2. Use "Find Nearby Merchants" to see restaurants near Menteng
3. Browse menu from a nearby merchant
4. Place order with delivery coordinates

### Scenario 3: Multi-Merchant Test

1. Login as `satesenayan@example.com` - see only Sate Senayan menu
2. Logout and login as `baksomalang@example.com` - see only Bakso Malang menu
3. Each merchant sees only their own data

### Scenario 4: Geolocation Features

1. Login as any customer account
2. Go to Merchants page
3. Click "Find Nearby" - browser will request location permission
4. View merchants sorted by distance from customer's location
5. See distance in kilometers for each merchant

### Scenario 5: Admin Overview

1. Login as: `admin@example.com`
2. Access all merchants data
3. View system-wide statistics
4. Manage users and merchants

---

## Database Seeding Command

To reset and reseed the database with fresh test data:

```bash
php artisan migrate:fresh --seed
```

This will:

-   Drop all tables
-   Run all migrations
-   Create 5 merchants
-   Create 1 admin, 5 merchant users, and 5 customer users
-   All with the default password: `password`

---

## Testing Notes

1. **Merchant Isolation**: Each merchant can only see/edit their own:

    - Food menu items
    - Orders
    - Restaurant details

2. **Geolocation**: All coordinates are set in central Jakarta area for testing nearby search functionality

3. **Role Verification**: Try accessing merchant features while logged in as customer to verify authorization

4. **Distance Calculation**: The Haversine formula calculates distances between customer and merchant locations

5. **Multi-tenant Testing**: Test that merchants cannot see each other's data by logging in as different merchants

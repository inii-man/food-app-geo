# Multi-Merchant Food Ordering Application with Geolocation

A comprehensive food ordering platform built with Laravel 11, featuring **multi-merchant support**, **geolocation-based restaurant discovery**, and **shopping cart ordering system**. Find nearby restaurants, view them on an interactive map, and place orders with ease.

## 🌟 Key Features

### Multi-Merchant System

-   ✅ Support for unlimited restaurants/merchants
-   ✅ Each merchant manages their own menus independently
-   ✅ Merchant registration with self-service onboarding
-   ✅ Customers can order from multiple restaurants
-   ✅ Merchant profiles with images, hours, and contact info
-   ✅ Per-merchant authentication and dashboard

### Shopping Cart & Ordering

-   ✅ **Shopping Cart System** - Add multiple items before checkout
-   ✅ **Quantity Management** - Adjust item quantities in cart
-   ✅ **Order Summary** - Review total before placing order
-   ✅ **Delivery Tracking** - Track orders with status updates
-   ✅ **Order History** - View past orders with details
-   ✅ **Distance Calculation** - Automatic delivery distance from merchant

### Geolocation Features

-   ✅ **Browser Geolocation API** - Automatic location detection
-   ✅ **Leaflet.js Integration** - Interactive maps with OpenStreetMap
-   ✅ **Haversine Formula** - Accurate distance calculation
-   ✅ **Nearby Search** - Find restaurants within specified radius (1-20km)
-   ✅ **Visual Maps** - See all restaurants on an interactive map
-   ✅ **Distance Display** - Know how far each restaurant is from you
-   ✅ **Location-based Ordering** - Set delivery coordinates for each order

### User Experience

-   ✅ User authentication (login/register)
-   ✅ Role-based access control (Admin, Merchant, Customer)
-   ✅ **Modern UI** - Bootstrap 5 with responsive design
-   ✅ **Image Upload & Preview** - For menus and merchant profiles
-   ✅ **Real-time Updates** - Dynamic cart updates and calculations
-   ✅ **Mobile-Friendly** - Works seamlessly on all devices

## 🎭 User Roles

### 👥 Customer

-   Browse merchants on interactive map
-   Search nearby restaurants by location
-   Add multiple items to shopping cart
-   Review and edit cart before checkout
-   Place orders with delivery coordinates
-   Track order status
-   Update profile with delivery address

### 👨‍🍳 Merchant

-   Register and create merchant profile
-   Manage food menu (CRUD operations)
-   Set menu item availability
-   View and manage incoming orders
-   Update order status (pending → processing → delivered)
-   Access merchant-specific dashboard

### 👑 Admin

-   Manage all merchants
-   View all food menus
-   Monitor all orders system-wide
-   Access comprehensive dashboard

## Requirements

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   MySQL or other database system
-   Laravel 11
-   Modern browser with geolocation support

## Installation

Follow these steps to set up the project locally:

### 1. Clone the repository

```bash
git clone https://github.com/inii-man/food-app-sederhana.git
cd food-app-sederhana
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Create environment file

```bash
cp .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Configure database

Edit the `.env` file and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 7. Run database migrations with seeders

```bash
php artisan migrate:fresh --seed
```

This will create:

-   5 test merchants in Jakarta area
-   1 admin account
-   5 merchant accounts (one per merchant)
-   5 customer accounts with different locations

### 8. Create storage symbolic link

```bash
php artisan storage:link
```

### 9. Build assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 10. Start the development server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 🔑 Default Test Accounts

After seeding, you can login with these accounts (password: `password`):

### Admin

```
Email: admin@example.com
Role: Full system access
```

### Merchants (5 accounts)

```
warungpadang@example.com  - Warung Padang Sederhana
satesenayan@example.com   - Sate Khas Senayan
baksomalang@example.com   - Bakso Malang Cak Eko
nasigoreng@example.com    - Nasi Goreng Kambing
ayamgeprek@example.com    - Ayam Geprek Bensu
```

### Customers (5 accounts)

```
customer1@example.com - Budi Santoso (Menteng)
customer2@example.com - Siti Nurhaliza (Cikini)
customer3@example.com - Ahmad Wijaya (Salemba)
customer4@example.com - Dewi Lestari (Matraman)
customer5@example.com - Rudi Hartono (Gatot Subroto)
```

**All passwords:** `password`

See `TEST_CREDENTIALS.md` for complete details.

## 🚀 Quick Start Guide

### For Customers:

1. Login as any customer account
2. Go to **🗺️ Peta Merchant** in navbar
3. Click "Cari Merchant Terdekat" to find nearby restaurants
4. Click on a merchant to view their menu
5. Add items to cart with "Tambah ke Keranjang"
6. Click **🛒 Keranjang** button to review order
7. Fill delivery details and submit order

### For Merchants:

1. Login as any merchant account
2. Go to **🍳 Menu Saya** to manage your food menu
3. Click "Tambah Menu Baru" to add items
4. Upload images and set prices
5. View incoming orders in **📦 Kelola Pesanan**
6. Update order status as you process them

### For New Merchants:

1. Click "Daftar Sebagai Merchant" on login page
2. Fill merchant registration form
3. Set your restaurant location on map
4. Account created automatically!
5. Login and start adding your menu

## 📱 Features in Detail

### Shopping Cart System

-   Add multiple menu items from a merchant
-   Adjust quantities with +/- buttons
-   Remove unwanted items
-   See real-time price calculations
-   Review order before submission
-   Toast notifications for cart actions

### Geolocation Features

-   Interactive Leaflet.js maps
-   Find nearby merchants within radius
-   Visual distance indicators
-   Color-coded markers (blue=you, red=merchant, green=nearby)
-   Click markers for merchant details
-   Direct links to order from map

### Order Management

-   Comprehensive order tracking
-   Status updates: Pending → Processing → Delivered
-   Distance calculation from merchant to delivery
-   Order notes support
-   Order history with filtering
-   Merchant-specific order views

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── MerchantRegisterController.php
│   │   ├── FoodMenuController.php
│   │   ├── MerchantController.php
│   │   ├── OrderController.php
│   │   └── ProfileController.php
│   └── Requests/
│       └── ProfileUpdateRequest.php
├── Models/
│   ├── FoodMenu.php
│   ├── Merchant.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── User.php
database/
├── migrations/
│   ├── create_merchants_table.php
│   ├── add_geolocation_to_users.php
│   ├── add_merchant_id_to_food_menus.php
│   └── add_merchant_id_to_orders.php
├── seeders/
│   ├── MerchantSeeder.php
│   └── UserSeeder.php
resources/
├── views/
│   ├── merchants/
│   │   ├── index.blade.php
│   │   ├── show.blade.php (with shopping cart)
│   │   ├── map.blade.php
│   │   └── nearby.blade.php
│   ├── foodmenu/
│   │   ├── index.blade.php
│   │   ├── create.blade.php (new UI)
│   │   └── edit.blade.php (new UI)
│   ├── orders/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── profile/
│       └── partials/
│           └── update-profile-information-form.blade.php
routes/
└── web.php
```

## 🔧 Development Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate:fresh --seed

# Clear specific caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Generate files
php artisan make:controller ControllerName
php artisan make:model ModelName -m
php artisan make:migration create_table_name

# Watch for file changes
npm run dev
```

## 📚 Documentation

-   `DOCUMENTATION.md` - Complete technical documentation
-   `QUICK_REFERENCE.md` - Developer quick reference
-   `TEST_CREDENTIALS.md` - All test account details
-   `MERCHANT_LOGIN_SYSTEM.md` - Merchant authentication guide
-   `PRESENTATION_SLIDES.md` - Project presentation

## 🆕 Recent Updates (November 2025)

### v2.0 - Shopping Cart & UI Overhaul

-   ✨ Shopping cart system for multiple item orders
-   🎨 Modern food menu create/edit forms
-   📍 Enhanced profile with location editing
-   🗺️ Improved merchant map interface
-   🛒 Real-time cart updates with badges
-   📱 Better mobile responsiveness
-   🔄 Deprecated old order flow
-   ✅ Bootstrap JS properly loaded

## 🐛 Troubleshooting

### Map not showing?

-   Check if Leaflet.js CDN is loaded
-   Ensure div has explicit height
-   Verify coordinates are valid numbers

### Geolocation not working?

-   Enable location permissions in browser
-   Use HTTPS (required by most browsers)
-   Provide manual coordinate input as fallback

### Orders not working?

-   Clear route cache: `php artisan route:clear`
-   Check merchant_id is set correctly
-   Verify order items array structure

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Credits

-   Laravel Framework
-   Leaflet.js for maps
-   OpenStreetMap for map tiles
-   Bootstrap 5 for UI components
-   Bootstrap Icons for icons

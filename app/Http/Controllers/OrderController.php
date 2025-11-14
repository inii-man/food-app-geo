<?php

namespace App\Http\Controllers;

use App\Models\FoodMenu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin: Tampilkan semua pesanan
            $orders = Order::with(['orderItems.foodMenu', 'user', 'merchant'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->isMerchant()) {
            // Merchant: Hanya pesanan untuk merchant mereka
            $orders = Order::where('merchant_id', $user->merchant_id)
                ->with(['orderItems.foodMenu', 'user', 'merchant'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Customer: Hanya pesanan mereka sendiri
            $orders = Order::where('user_id', Auth::id())
                ->with(['orderItems.foodMenu', 'user', 'merchant'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $foodItems = FoodMenu::all(); // Ambil semua item menu
        return view('orders.menu', compact('foodItems'));
    }

    public function store(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'delivery_address' => 'required|string|max:500',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.food_menu_id' => 'required|exists:food_menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Anda harus memilih setidaknya satu item makanan.',
            'items.min' => 'Anda harus memilih setidaknya satu item makanan.',
            'delivery_address.required' => 'Alamat pengiriman wajib diisi.',
        ]);

        // Calculate total price
        $totalPrice = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $foodMenu = FoodMenu::findOrFail($item['food_menu_id']);
            $quantity = $item['quantity'];
            $price = $foodMenu->price;

            $totalPrice += $price * $quantity;
            $orderItems[] = [
                'food_menu_id' => $item['food_menu_id'],
                'quantity' => $quantity,
                'price' => $price,
            ];
        }

        // Calculate distance if coordinates provided
        $distance = null;
        if ($validated['delivery_latitude'] && $validated['delivery_longitude']) {
            $merchant = \App\Models\Merchant::findOrFail($validated['merchant_id']);
            if ($merchant->latitude && $merchant->longitude) {
                $distance = $this->calculateDistance(
                    $merchant->latitude,
                    $merchant->longitude,
                    $validated['delivery_latitude'],
                    $validated['delivery_longitude']
                );
            }
        }

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'merchant_id' => $validated['merchant_id'],
            'total_price' => $totalPrice,
            'status' => 'pending',
            'address' => $validated['delivery_address'],
            'delivery_latitude' => $validated['delivery_latitude'],
            'delivery_longitude' => $validated['delivery_longitude'],
            'distance_km' => $distance,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create order items
        foreach ($orderItems as $item) {
            $order->orderItems()->create($item);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat!');
    }

    // Calculate distance using Haversine formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        // Merchant: Hanya bisa lihat pesanan merchant mereka
        // Admin: Bisa lihat semua pesanan
        // Customer: Hanya pesanan mereka sendiri
        if ($user->isMerchant()) {
            if ($order->merchant_id != $user->merchant_id) {
                abort(403, 'Anda tidak memiliki akses untuk melihat pesanan ini.');
            }
        } elseif ($user->isCustomer()) {
            if ($order->user_id != Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk melihat pesanan ini.');
            }
        }

        return view('orders.show', compact('order'));
    }

    // Merchant: View all orders
    public function merchantIndex()
    {
        // Ambil semua pesanan untuk merchant
        $orders = Order::with(['orderItems.foodMenu', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.merchant-index', compact('orders'));
    }

    // Merchant: Update order status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}

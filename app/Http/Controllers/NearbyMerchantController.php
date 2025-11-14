<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;

class NearbyMerchantController extends Controller
{
    /**
     * Display nearby merchants based on user's location.
     */
    public function index(Request $request)
    {
        return view('merchants.nearby');
    }

    /**
     * Search for nearby merchants using AJAX.
     */
    public function search(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10; // Default 10km

        $merchants = Merchant::nearby($latitude, $longitude, $radius)
            ->where('is_active', true)
            ->with('foodMenus')
            ->get();

        // Add formatted distance
        $merchants->each(function ($merchant) {
            $merchant->formatted_distance = number_format($merchant->distance, 2) . ' km';
        });

        return response()->json([
            'success' => true,
            'merchants' => $merchants,
            'user_location' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]
        ]);
    }

    /**
     * Get all active merchants for map display.
     */
    public function all()
    {
        $merchants = Merchant::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('foodMenus')
            ->get();

        return response()->json([
            'success' => true,
            'merchants' => $merchants
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $merchants = Merchant::with('foodMenus')->paginate(10);
        return view('merchants.index', compact('merchants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('merchants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('merchants', 'public');
        }

        Merchant::create($validated);

        return redirect()->route('merchants.index')
            ->with('success', 'Merchant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Merchant $merchant)
    {
        $merchant->load(['foodMenus' => function ($query) {
            $query->where('is_available', true);
        }]);
        return view('merchants.show', compact('merchant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Merchant $merchant)
    {
        return view('merchants.edit', compact('merchant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Merchant $merchant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($merchant->image) {
                Storage::disk('public')->delete($merchant->image);
            }
            $validated['image'] = $request->file('image')->store('merchants', 'public');
        }

        $merchant->update($validated);

        return redirect()->route('merchants.index')
            ->with('success', 'Merchant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchant $merchant)
    {
        if ($merchant->image) {
            Storage::disk('public')->delete($merchant->image);
        }

        $merchant->delete();

        return redirect()->route('merchants.index')
            ->with('success', 'Merchant deleted successfully.');
    }

    /**
     * Find nearby merchants based on user location.
     */
    public function nearby(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50',
        ]);

        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];
        $radius = $validated['radius'] ?? 10; // Default 10 km

        $merchants = Merchant::where('is_active', true)
            ->nearby($latitude, $longitude, $radius)
            ->with('foodMenus')
            ->get();

        // Add distance to each merchant
        $merchants->each(function ($merchant) use ($latitude, $longitude) {
            $merchant->distance = $merchant->distanceFrom($latitude, $longitude);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'merchants' => $merchants,
                'user_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]
            ]);
        }

        return view('merchants.nearby', compact('merchants', 'latitude', 'longitude'));
    }

    /**
     * Show map view of all merchants.
     */
    public function map()
    {
        $merchants = Merchant::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('merchants.map', compact('merchants'));
    }
}

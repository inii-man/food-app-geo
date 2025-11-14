<?php

namespace App\Http\Controllers;

use App\Models\FoodMenu;
use App\Models\Merchant;
use Illuminate\Http\Request;

class FoodMenuController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isMerchant() && $user->merchant_id) {
            // Merchant only sees their own menus
            $foodMenus = FoodMenu::where('merchant_id', $user->merchant_id)->get();
        } else {
            // Admin sees all menus
            $foodMenus = FoodMenu::with('merchant')->get();
        }

        return view('foodmenu.index', compact('foodMenus'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->isMerchant() && !$user->merchant_id) {
            return redirect()->route('foodmenu.index')
                ->with('error', 'You must be associated with a merchant to create menus.');
        }

        $merchants = Merchant::where('is_active', true)->get();
        return view('foodmenu.create', compact('merchants'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'merchant_id' => $user->isAdmin() ? 'required|exists:merchants,id' : 'nullable',
            'is_available' => 'nullable|boolean',
        ]);

        // Handle is_available checkbox
        $validated['is_available'] = $request->has('is_available') ? (bool)$request->is_available : true;

        // Auto-assign merchant_id for merchant users
        if ($user->isMerchant()) {
            $validated['merchant_id'] = $user->merchant_id;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('food_images', 'public');
            $validated['image'] = $path;
        }

        FoodMenu::create($validated);

        return redirect()->route('foodmenu.index')->with('success', 'Food menu item created successfully.');
    }

    public function edit($id)
    {
        $foodMenu = FoodMenu::findOrFail($id);
        $user = auth()->user();

        // Check authorization
        if ($user->isMerchant() && $foodMenu->merchant_id != $user->merchant_id) {
            abort(403, 'Unauthorized action.');
        }

        $merchants = Merchant::where('is_active', true)->get();
        return view('foodmenu.edit', compact('foodMenu', 'merchants'));
    }

    public function update(Request $request, $id)
    {
        $foodMenu = FoodMenu::findOrFail($id);
        $user = auth()->user();

        // Check authorization
        if ($user->isMerchant() && $foodMenu->merchant_id != $user->merchant_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'merchant_id' => $user->isAdmin() ? 'required|exists:merchants,id' : 'nullable',
            'is_available' => 'nullable|boolean',
        ]);

        // Handle is_available checkbox
        $validated['is_available'] = $request->has('is_available') ? (bool)$request->is_available : true;

        // Merchant users can't change merchant_id
        if ($user->isMerchant()) {
            unset($validated['merchant_id']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('food_images', 'public');
            $validated['image'] = $path;
        }

        $foodMenu->update($validated);

        return redirect()->route('foodmenu.index')->with('success', 'Food menu item updated successfully.');
    }

    public function destroy($id)
    {
        $foodMenu = FoodMenu::findOrFail($id);
        $user = auth()->user();

        // Check authorization
        if ($user->isMerchant() && $foodMenu->merchant_id != $user->merchant_id) {
            abort(403, 'Unauthorized action.');
        }

        $foodMenu->delete();

        return redirect()->route('foodmenu.index')->with('success', 'Food menu item deleted successfully.');
    }
}

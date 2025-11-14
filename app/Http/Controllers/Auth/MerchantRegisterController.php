<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MerchantRegisterController extends Controller
{
    /**
     * Show the merchant registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.merchant-register');
    }

    /**
     * Handle merchant registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            // User data
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            // Merchant data
            'merchant_name' => 'required|string|max:255',
            'merchant_description' => 'nullable|string',
            'merchant_address' => 'required|string',
            'merchant_phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
        ]);

        DB::beginTransaction();

        try {
            // Create merchant first
            $merchant = Merchant::create([
                'name' => $validated['merchant_name'],
                'description' => $validated['merchant_description'] ?? null,
                'address' => $validated['merchant_address'],
                'phone' => $validated['merchant_phone'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'opening_time' => $validated['opening_time'] ?? null,
                'closing_time' => $validated['closing_time'] ?? null,
                'is_active' => true,
            ]);

            // Create user and link to merchant
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'merchant',
                'merchant_id' => $merchant->id,
            ]);

            DB::commit();

            // Login the user
            auth()->login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Akun merchant berhasil dibuat! Anda sekarang dapat mengelola restoran Anda.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat akun merchant. Silakan coba lagi.']);
        }
    }
}

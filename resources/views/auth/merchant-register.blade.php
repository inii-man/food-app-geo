<x-guest-layout>
    <form method="POST" action="{{ route('merchant.register') }}">
        @csrf

        <h2 class="text-2xl font-bold mb-6 text-center">Daftar Sebagai Merchant</h2>

        @if($errors->has('error'))
            <div class="alert alert-danger mb-4">
                {{ $errors->first('error') }}
            </div>
        @endif

        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold mb-3">Informasi Akun</h3>

            <!-- Name -->
            <div class="mb-3">
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-3">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold mb-3">Informasi Restoran</h3>

            <!-- Merchant Name -->
            <div class="mb-3">
                <x-input-label for="merchant_name" :value="__('Nama Restoran')" />
                <x-text-input id="merchant_name" class="block mt-1 w-full" type="text" name="merchant_name" :value="old('merchant_name')" required />
                <x-input-error :messages="$errors->get('merchant_name')" class="mt-2" />
            </div>

            <!-- Merchant Description -->
            <div class="mb-3">
                <x-input-label for="merchant_description" :value="__('Deskripsi Restoran')" />
                <textarea id="merchant_description" name="merchant_description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('merchant_description') }}</textarea>
                <x-input-error :messages="$errors->get('merchant_description')" class="mt-2" />
            </div>

            <!-- Merchant Address -->
            <div class="mb-3">
                <x-input-label for="merchant_address" :value="__('Alamat Lengkap')" />
                <textarea id="merchant_address" name="merchant_address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2" required>{{ old('merchant_address') }}</textarea>
                <x-input-error :messages="$errors->get('merchant_address')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <x-input-label for="merchant_phone" :value="__('Nomor Telepon')" />
                <x-text-input id="merchant_phone" class="block mt-1 w-full" type="text" name="merchant_phone" :value="old('merchant_phone')" />
                <x-input-error :messages="$errors->get('merchant_phone')" class="mt-2" />
            </div>
        </div>

        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold mb-3">Lokasi Restoran (Opsional)</h3>
            <p class="text-sm text-gray-600 mb-3">Koordinat untuk menampilkan restoran di peta</p>

            <div class="row mb-3">
                <div class="col-md-6">
                    <x-input-label for="latitude" :value="__('Latitude')" />
                    <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="0.00000001" name="latitude" :value="old('latitude')" />
                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                </div>
                <div class="col-md-6">
                    <x-input-label for="longitude" :value="__('Longitude')" />
                    <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="0.00000001" name="longitude" :value="old('longitude')" />
                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-secondary" onclick="getMyLocation()">
                <i class="bi bi-geo-alt"></i> Gunakan Lokasi Saat Ini
            </button>
        </div>

        <div class="pb-4 mb-4">
            <h3 class="text-lg font-semibold mb-3">Jam Operasional (Opsional)</h3>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <x-input-label for="opening_time" :value="__('Jam Buka')" />
                    <x-text-input id="opening_time" class="block mt-1 w-full" type="time" name="opening_time" :value="old('opening_time')" />
                    <x-input-error :messages="$errors->get('opening_time')" class="mt-2" />
                </div>
                <div class="col-md-6 mb-3">
                    <x-input-label for="closing_time" :value="__('Jam Tutup')" />
                    <x-text-input id="closing_time" class="block mt-1 w-full" type="time" name="closing_time" :value="old('closing_time')" />
                    <x-input-error :messages="$errors->get('closing_time')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar Sebagai Merchant') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function getMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                    document.getElementById('longitude').value = position.coords.longitude.toFixed(8);
                    alert('Lokasi berhasil didapatkan!');
                }, function(error) {
                    alert('Tidak dapat mendapatkan lokasi: ' + error.message);
                });
            } else {
                alert('Browser Anda tidak mendukung Geolocation');
            }
        }
    </script>
</x-guest-layout>

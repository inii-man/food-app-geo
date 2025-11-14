<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Peta Merchant - Cari Restoran Terdekat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search Controls -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="radius" class="form-label">Radius Pencarian (km)</label>
                                    <select class="form-select" id="radius">
                                        <option value="1">1 km</option>
                                        <option value="2">2 km</option>
                                        <option value="5" selected>5 km</option>
                                        <option value="10">10 km</option>
                                        <option value="20">20 km</option>
                                    </select>
                                </div>
                                <div class="col-md-8 d-flex align-items-end">
                                    <button type="button" class="btn btn-success w-100" onclick="findMyLocation()">
                                        <i class="bi bi-geo-alt-fill"></i> Cari Merchant Terdekat
                                    </button>
                                </div>
                            </div>
                            <div id="location-status" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div id="map" style="height: 600px; width: 100%; border-radius: 8px;"></div>

                    <!-- Merchant Legend -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Legenda:</h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div><span style="color: blue; font-size: 1.5rem;">●</span> Lokasi Anda</div>
                                        <div><span style="color: red; font-size: 1.5rem;">●</span> Merchant</div>
                                        <div><span style="color: green; font-size: 1.5rem;">●</span> Merchant dalam Radius</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Cara Menggunakan:</h6>
                                    <ol class="small">
                                        <li>Pilih radius pencarian</li>
                                        <li>Klik tombol "Cari Merchant Terdekat"</li>
                                        <li>Izinkan browser mengakses lokasi Anda</li>
                                        <li>Klik marker hijau untuk melihat merchant terdekat</li>
                                        <li>Klik "Lihat Menu" untuk memesan</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize map centered on Indonesia
        const map = L.map('map').setView([-6.2088, 106.8456], 12);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Custom icons
        const userIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const merchantIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const nearbyIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        let userMarker = null;
        let radiusCircle = null;
        let merchantMarkers = [];

        // Add all merchants to map
        const merchants = @json($merchants);
        merchants.forEach(merchant => {
            if (merchant.latitude && merchant.longitude) {
                const marker = L.marker([merchant.latitude, merchant.longitude], {icon: merchantIcon})
                    .addTo(map)
                    .bindPopup(`
                        <div style="min-width: 200px;">
                            <h6><strong>${merchant.name}</strong></h6>
                            <p class="mb-1"><small>${merchant.address}</small></p>
                            ${merchant.phone ? `<p class="mb-1"><small><i class="bi bi-telephone"></i> ${merchant.phone}</small></p>` : ''}
                            ${merchant.opening_time && merchant.closing_time ? 
                                `<p class="mb-1"><small><i class="bi bi-clock"></i> ${merchant.opening_time} - ${merchant.closing_time}</small></p>` : ''}
                            <p class="mb-2">
                                <span class="badge ${merchant.is_active ? 'bg-success' : 'bg-secondary'}">
                                    ${merchant.is_active ? 'Buka' : 'Tutup'}
                                </span>
                            </p>
                            <a href="/merchants/${merchant.id}" class="btn btn-sm btn-primary">Lihat Menu</a>
                        </div>
                    `);
                merchantMarkers.push({marker: marker, data: merchant});
            }
        });

        // Fit map to show all merchants
        if (merchants.length > 0) {
            const bounds = merchants
                .filter(m => m.latitude && m.longitude)
                .map(m => [m.latitude, m.longitude]);
            if (bounds.length > 0) {
                map.fitBounds(bounds, {padding: [50, 50]});
            }
        }

        function findMyLocation() {
            const statusDiv = document.getElementById('location-status');
            statusDiv.innerHTML = '<div class="alert alert-info"><div class="spinner-border spinner-border-sm" role="status"></div> Mendapatkan lokasi Anda...</div>';

            if (!navigator.geolocation) {
                statusDiv.innerHTML = '<div class="alert alert-danger">Browser Anda tidak mendukung Geolocation</div>';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const radius = document.getElementById('radius').value * 1000; // Convert to meters

                    statusDiv.innerHTML = '<div class="alert alert-success">Lokasi ditemukan! Menampilkan merchant terdekat...</div>';

                    // Remove previous user marker and circle
                    if (userMarker) {
                        map.removeLayer(userMarker);
                    }
                    if (radiusCircle) {
                        map.removeLayer(radiusCircle);
                    }

                    // Add user location marker
                    userMarker = L.marker([lat, lng], {icon: userIcon})
                        .addTo(map)
                        .bindPopup('<strong>Lokasi Anda</strong>')
                        .openPopup();

                    // Add radius circle
                    radiusCircle = L.circle([lat, lng], {
                        color: 'blue',
                        fillColor: '#30a3ff',
                        fillOpacity: 0.1,
                        radius: radius
                    }).addTo(map);

                    // Center map on user location
                    map.setView([lat, lng], 13);

                    // Calculate distances and highlight nearby merchants
                    merchantMarkers.forEach(({marker, data}) => {
                        if (data.latitude && data.longitude) {
                            const distance = calculateDistance(lat, lng, data.latitude, data.longitude);
                            const radiusKm = radius / 1000;

                            if (distance <= radiusKm) {
                                // Change icon to green for nearby merchants
                                marker.setIcon(nearbyIcon);
                                marker.setPopupContent(`
                                    <div style="min-width: 200px;">
                                        <h6><strong>${data.name}</strong></h6>
                                        <p class="mb-1"><small>${data.address}</small></p>
                                        <p class="mb-2">
                                            <span class="badge bg-primary">
                                                <i class="bi bi-geo-alt"></i> ${distance.toFixed(2)} km
                                            </span>
                                        </p>
                                        <a href="/merchants/${data.id}" class="btn btn-sm btn-primary">Lihat Menu</a>
                                    </div>
                                `);
                            } else {
                                // Reset to red for far merchants
                                marker.setIcon(merchantIcon);
                            }
                        }
                    });

                    setTimeout(() => {
                        statusDiv.innerHTML = '';
                    }, 3000);
                },
                function(error) {
                    let errorMsg = 'Tidak dapat mendapatkan lokasi Anda.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = 'Anda menolak permintaan lokasi.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            errorMsg = 'Permintaan lokasi timeout.';
                            break;
                    }
                    statusDiv.innerHTML = `<div class="alert alert-danger">${errorMsg}</div>`;
                }
            );
        }

        // Haversine formula to calculate distance between two points
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in km
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function toRad(degrees) {
            return degrees * Math.PI / 180;
        }
    </script>
</x-app-layout>

<x-app-layout>
   <div class="container mt-5">
    <h1 class="mb-4 text-center">Pilih Menu Makanan Anda</h1>

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf
        <div class="row">
            @foreach($foodItems as $foodItem)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('storage/' . $foodItem->image) }}"class="card-img-top" alt="{{ $foodItem->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $foodItem->name }}</h5>
                        <p class="card-text text-muted">{{ $foodItem->description }}</p>
                        <p class="card-text mt-auto"><b>Harga: Rp{{ number_format($foodItem->price, 0, ',', '.') }}</b></p>
                        <div class="input-group mt-2">
                            <span class="input-group-text">Jumlah</span>
                            <input type="number" name="items[{{ $foodItem->id }}][quantity]" class="form-control item-quantity" value="0" min="0" data-price="{{ $foodItem->price }}">
                            <input type="hidden" name="items[{{ $foodItem->id }}][food_menu_id]" value="{{ $foodItem->id }}">
                            <input type="hidden" name="items[{{ $foodItem->id }}][price]" value="{{ $foodItem->price }}">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <hr>

        <div class="mb-3">
            <label for="address" class="form-label">Alamat Pengiriman:</label>
            <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
            @error('address')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Total Harga: Rp<span id="totalPrice">0</span></h4>
            <input type="hidden" name="total_price" id="hiddenTotalPrice" value="0">
            <button type="submit" class="btn btn-success btn-lg">Pesan Sekarang</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const orderForm = document.getElementById('orderForm');
        const itemQuantities = document.querySelectorAll('.item-quantity');
        const totalPriceSpan = document.getElementById('totalPrice');
        const hiddenTotalPriceInput = document.getElementById('hiddenTotalPrice');

        function calculateTotalPrice() {
            let total = 0;
            itemQuantities.forEach(input => {
                const quantity = parseInt(input.value);
                const price = parseFloat(input.dataset.price);
                if (!isNaN(quantity) && quantity > 0) {
                    total += quantity * price;
                }
            });
            totalPriceSpan.textContent = total.toLocaleString('id-ID');
            hiddenTotalPriceInput.value = total;
        }

        itemQuantities.forEach(input => {
            input.addEventListener('change', calculateTotalPrice);
            input.addEventListener('keyup', calculateTotalPrice);
        });

        // Initial calculation
        calculateTotalPrice();

        orderForm.addEventListener('submit', function(event) {
            let totalItemsSelected = 0;
            itemQuantities.forEach(input => {
                if (parseInt(input.value) > 0) {
                    totalItemsSelected++;
                }
            });

            if (totalItemsSelected === 0) {
                alert('Anda harus memilih setidaknya satu item makanan.');
                event.preventDefault();
            }
        });
    });
</script>
</x-app-layout>
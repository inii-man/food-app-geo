<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="bi bi-pencil-square"></i> Edit Menu
            </h2>
            <a href="{{ route('foodmenu.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('foodmenu.update', $foodMenu->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nama Menu <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $foodMenu->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Deskripsi <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              name="description" 
                                              id="description" 
                                              rows="4"
                                              required>{{ old('description', $foodMenu->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label fw-bold">Harga <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control @error('price') is-invalid @enderror" 
                                                   name="price" 
                                                   id="price" 
                                                   value="{{ old('price', $foodMenu->price) }}"
                                                   min="0"
                                                   step="500"
                                                   required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="is_available" class="form-label fw-bold">Status Ketersediaan</label>
                                        <select class="form-select @error('is_available') is-invalid @enderror" 
                                                name="is_available" 
                                                id="is_available">
                                            <option value="1" {{ old('is_available', $foodMenu->is_available) == 1 ? 'selected' : '' }}>Tersedia</option>
                                            <option value="0" {{ old('is_available', $foodMenu->is_available) == 0 ? 'selected' : '' }}>Tidak Tersedia</option>
                                        </select>
                                        @error('is_available')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-bold">Gambar Menu Baru</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           name="image" 
                                           id="image"
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar</small>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label fw-bold">Preview Gambar</label>
                                    <div id="imagePreview" class="border rounded p-3 text-center bg-light">
                                        @if($foodMenu->image)
                                            <img src="{{ asset('storage/' . $foodMenu->image) }}" class="img-fluid rounded" alt="{{ $foodMenu->name }}" style="max-height: 300px; object-fit: cover;">
                                        @else
                                            <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Belum ada gambar</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <form action="{{ route('foodmenu.destroy', $foodMenu->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Hapus Menu
                                </button>
                            </form>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('foodmenu.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Update Menu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            const imagePreview = document.getElementById('imagePreview');
            
            reader.onload = function() {
                imagePreview.innerHTML = `
                    <img src="${reader.result}" class="img-fluid rounded" alt="Preview" style="max-height: 300px; object-fit: cover;">
                `;
            }
            
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</x-app-layout>

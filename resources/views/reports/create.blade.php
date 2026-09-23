<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporkan Kerusakan Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                    <p class="font-semibold mb-1">Terdapat kesalahan pengisian:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Pilihan Fasilitas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Fasilitas</label>
                    <select name="id_fasilitas" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Fasilitas --</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id_fasilitas }}" {{ old('id_fasilitas') == $facility->id_fasilitas ? 'selected' : '' }}>
                                {{ $facility->facility_name }} ({{ $facility->type }} - {{ $facility->location }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kategori Kerusakan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Kerusakan</label>
                    <select name="id_kategori" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id_kategori }}" {{ old('id_kategori') == $category->id_kategori ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Deskripsi Kerusakan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kerusakan</label>
                    <textarea name="description" rows="4" required placeholder="Jelaskan kerusakan yang terjadi pada fasilitas..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                {{-- Foto Kerusakan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kerusakan (opsional, bisa lebih dari 1)</label>
                    <input type="file" name="photos[]" accept="image/*" multiple
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG/PNG/GIF/WebP, maksimal 2 MB per foto, maksimal 5 foto.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
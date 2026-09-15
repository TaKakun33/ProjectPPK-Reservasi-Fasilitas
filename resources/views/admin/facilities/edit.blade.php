<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Fasilitas') }}
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

            <form method="POST" action="{{ route('admin.fasilitas.update', $fasilitas->id_fasilitas) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Fasilitas</label>
                    <input type="text" name="facility_name" value="{{ old('facility_name', $fasilitas->facility_name) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <input type="text" name="type" value="{{ old('type', $fasilitas->type) }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $fasilitas->capacity) }}" required min="1"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $fasilitas->location) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $fasilitas->description) }}</textarea>
                </div>

                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Status Aktif:</span>
                        @if($fasilitas->is_active)
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.fasilitas.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition">
                        Perbarui Fasilitas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

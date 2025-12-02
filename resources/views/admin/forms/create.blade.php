{{-- resources/views/admin/forms/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Form Baru
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.forms.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Nama Form / Campaign
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Contoh: Hijab Bergo Paket 10"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Deskripsi (opsional)
                            </label>
                            <textarea
                                name="description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Deskripsi singkat campaign, misalnya benefit utama, target audiens, dll."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Harga Utama (opsional)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                name="base_price"
                                value="{{ old('base_price') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Contoh: 149000"
                            >
                            @error('base_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input
                                id="is_active"
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                {{ old('is_active', true) ? 'checked' : '' }}
                            >
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Form aktif (bisa menerima order)
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a
                                href="{{ route('admin.forms.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                Batal
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Simpan Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

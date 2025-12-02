<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Field untuk: {{ $form->name }}
                </h2>
                <p class="text-sm text-gray-500">
                    Atur field yang akan muncul di form embed.
                </p>
            </div>
            <a href="{{ route('admin.forms.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke daftar form
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-3 rounded-md bg-green-50 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Daftar field --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Field aktif</h3>

                @if ($fields->isEmpty())
                    <p class="text-sm text-gray-600">
                        Belum ada field. Tambahkan field di form di bawah.
                    </p>
                @else
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left">Label</th>
                                <th class="px-3 py-2 text-left">Name</th>
                                <th class="px-3 py-2 text-left">Tipe</th>
                                <th class="px-3 py-2 text-left">Required</th>
                                <th class="px-3 py-2 text-left"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fields as $field)
                                <tr class="border-b">
                                    <td class="px-3 py-2">{{ $field->label }}</td>
                                    <td class="px-3 py-2 text-xs font-mono text-gray-600">
                                        {{ $field->name }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-700">
                                        {{ $field->type }}
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        {{ $field->required ? 'Ya' : 'Tidak' }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <form
                                            action="{{ route('admin.forms.fields.destroy', [$form, $field]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus field ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="text-xs text-red-600 hover:text-red-800"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Form tambah field --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Tambah field baru</h3>

                <form method="POST"
                      action="{{ route('admin.forms.fields.store', $form) }}"
                      class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Label
                        </label>
                        <input
                            type="text"
                            name="label"
                            value="{{ old('label') }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Contoh: Nama Lengkap"
                        >
                        @error('label')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Name (untuk HTML)
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="contoh: full_name, phone, address"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Tipe field
                            </label>
                            <select
                                name="type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            >
                                <option value="">Pilih tipe</option>
                                @foreach ($fieldTypes as $type)
                                    <option value="{{ $type }}" @selected(old('type') === $type)>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Opsi (untuk SELECT, pisahkan dengan koma)
                            </label>
                            <input
                                type="text"
                                name="options"
                                value="{{ old('options') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Contoh: Paket A, Paket B, Paket C"
                            >
                            @error('options')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center mt-6">
                            <input
                                id="required"
                                type="checkbox"
                                name="required"
                                value="1"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                {{ old('required') ? 'checked' : '' }}
                            >
                            <label for="required" class="ml-2 block text-sm text-gray-700">
                                Wajib diisi
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Tambah Field
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

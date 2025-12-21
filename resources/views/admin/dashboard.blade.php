{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Selamat datang di FormOrderX</h1>
                    <p class="mb-4">
                        Ini adalah dashboard utama. Nantinya di sini akan muncul:
                    </p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Ringkasan order</li>
                        <li>Jumlah form aktif</li>
                        <li>Statistik konversi</li>
                        <li>Menu ke Form Builder, Order List, Tracking, dll.</li>
                    </ul>
                    <a
    href="{{ route('app.forms.index') }}"
    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700"
>
    Kelola Form
</a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

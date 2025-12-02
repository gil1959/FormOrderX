{{-- resources/views/landing.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50">
        <div class="max-w-3xl mx-auto text-center px-4">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">
                FormOrderX – Custom Form Order untuk Iklan Berbayar
            </h1>
            <p class="text-lg text-gray-600 mb-6">
                Sistem form order mandiri pengganti OrderOnline. Setiap client punya dashboard sendiri
                untuk membuat form, generate embed code, mengelola order, dan tracking konversi dengan
                proteksi anti-spam tingkat lanjut.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition"
                >
                    Login Admin
                </a>
                <a
                    href="{{ route('register') }}"
                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                >
                    Daftar Client Baru
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-left">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <h2 class="font-semibold text-gray-900 mb-2">Form Builder & Embed</h2>
                    <p class="text-sm text-gray-600">
                        Buat banyak form untuk campaign berbeda dan dapatkan script embed siap tempel
                        ke Berdu, WordPress, atau landing page custom.
                    </p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <h2 class="font-semibold text-gray-900 mb-2">Anti-Spam Tingkat Lanjut</h2>
                    <p class="text-sm text-gray-600">
                        Honeypot, time-gate, reCAPTCHA, dan limit submit per IP menjaga data tetap
                        bersih dari bot dan spam.
                    </p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <h2 class="font-semibold text-gray-900 mb-2">Tracking Pixel & Order</h2>
                    <p class="text-sm text-gray-600">
                        Pantau order dan event konversi (Lead, Purchase) untuk Meta, TikTok, dan
                        Google Ads langsung dari satu dashboard.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

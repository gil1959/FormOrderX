@extends('layouts.marketing')

@section('content')
  <x-marketing-navbar />

  <!-- HERO -->
  <section class="container-pad pt-14 pb-10">
    <div class="grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <span class="badge badge-indigo">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3 7h7l-5.5 4.1L18 21l-6-4-6 4 1.5-7.9L2 9h7z"/>
          </svg>
          Antarmuka modern • tampilan cerah
        </span>

        <h1 class="mt-5 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900">
          Kelola form dan order dalam satu sistem yang rapi dan terstruktur.
        </h1>

        <p class="mt-4 text-lg text-slate-600 leading-relaxed">
          Buat form, pantau order masuk, dan tindak lanjuti sesi yang belum selesai
          melalui tampilan yang jelas, konsisten, dan nyaman digunakan setiap hari.
        </p>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          @auth
            <a class="btn-primary" href="{{ route('app.dashboard') }}">
              Buka Dashboard
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14"/><path d="M13 5l7 7-7 7"/>
              </svg>
            </a>
          @else
            <a class="btn-primary" href="{{ route('login') }}">Masuk</a>
            <a class="btn-outline" href="{{ route('register') }}">Buat Akun</a>
          @endauth
        </div>

        <div class="mt-7 grid sm:grid-cols-3 gap-3">
          <div class="card p-4">
            <p class="text-xs font-semibold text-slate-500">Fokus</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">Form & order</p>
          </div>
          <div class="card p-4">
            <p class="text-xs font-semibold text-slate-500">Konsistensi</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">Komponen UI seragam</p>
          </div>
          <div class="card p-4">
            <p class="text-xs font-semibold text-slate-500">Kecepatan</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">Aksi cepat dari dashboard</p>
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL / PREVIEW -->
      <div class="relative">
        <div class="card p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="icon-bubble icon-bubble-indigo">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                  <path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h6"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-900">Ringkasan aktivitas</p>
                <p class="text-xs text-slate-500">Gambaran cepat kondisi hari ini</p>
              </div>
            </div>
            <span class="badge badge-emerald">Terpantau</span>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div class="card-solid p-4">
              <p class="text-xs font-semibold text-slate-500">Form</p>
              <p class="mt-2 text-2xl font-extrabold text-slate-900">12</p>
              <p class="text-xs text-slate-500 mt-1">tersedia</p>
            </div>
            <div class="card-solid p-4">
              <p class="text-xs font-semibold text-slate-500">Order</p>
              <p class="mt-2 text-2xl font-extrabold text-slate-900">318</p>
              <p class="text-xs text-slate-500 mt-1">7 hari</p>
            </div>
            <div class="card-solid p-4">
              <p class="text-xs font-semibold text-slate-500">Sesi</p>
              <p class="mt-2 text-2xl font-extrabold text-slate-900">23</p>
              <p class="text-xs text-slate-500 mt-1">belum selesai</p>
            </div>
          </div>

          <div class="mt-5 card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Aksi cepat</p>
            <div class="mt-3 grid sm:grid-cols-2 gap-2">
              <button class="btn-soft justify-start" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 5v14"/><path d="M5 12h14"/>
                </svg>
                Buat form baru
              </button>
              <button class="btn-soft justify-start" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <path d="M7 10l5 5 5-5"/>
                  <path d="M12 15V3"/>
                </svg>
                Export data
              </button>
            </div>
          </div>
        </div>

        <div class="pointer-events-none absolute -bottom-6 -left-6 h-24 w-24 rounded-3xl bg-amber-300/30 blur-2xl"></div>
      </div>
    </div>
  </section>

  <!-- TRUST / HIGHLIGHTS -->
  <section class="container-pad py-10">
    <div class="grid lg:grid-cols-3 gap-4">
      <div class="card p-6">
        <p class="text-lg font-extrabold text-slate-900">Tampilan konsisten</p>
        <p class="mt-2 text-sm text-slate-600">
          Komponen UI seragam (button, card, badge, input) agar pengalaman pengguna stabil di semua halaman.
        </p>
      </div>
      <div class="card p-6">
        <p class="text-lg font-extrabold text-slate-900">Navigasi jelas</p>
        <p class="mt-2 text-sm text-slate-600">
          Struktur halaman dibuat untuk mempercepat pencarian menu dan mengurangi kebingungan saat operasional.
        </p>
      </div>
      <div class="card p-6">
        <p class="text-lg font-extrabold text-slate-900">Siap dipakai harian</p>
        <p class="mt-2 text-sm text-slate-600">
          Kontras warna dan spacing diatur agar nyaman digunakan dalam durasi panjang.
        </p>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section id="features" class="container-pad py-12">
    <div>
      <span class="badge badge-amber">Fitur utama</span>
      <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Fokus pada alur kerja yang benar.</h2>
      <p class="mt-2 text-slate-600">
        Fitur dirancang untuk membantu pengelolaan form, order, dan tindak lanjut secara terstruktur.
      </p>
    </div>

    <div class="mt-8 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      @php
        $features = [
          ['title' => 'Manajemen form', 'desc' => 'Pembuatan dan pengelolaan form dengan tampilan yang rapi.', 'tone' => 'indigo'],
          ['title' => 'Daftar order', 'desc' => 'Pantau order masuk dan akses detailnya dengan cepat.', 'tone' => 'emerald'],
          ['title' => 'Sesi belum selesai', 'desc' => 'Identifikasi sesi yang berhenti di tengah untuk tindak lanjut.', 'tone' => 'rose'],
          ['title' => 'Dashboard ringkas', 'desc' => 'Ringkasan metrik dan shortcut untuk aksi paling sering digunakan.', 'tone' => 'amber'],
          ['title' => 'Navigasi responsif', 'desc' => 'Nyaman dipakai di desktop maupun mobile tanpa terasa sempit.', 'tone' => 'indigo'],
          ['title' => 'Konsistensi visual', 'desc' => 'Ikon, spacing, dan gaya komponen seragam di seluruh halaman.', 'tone' => 'emerald'],
        ];
      @endphp

      @foreach($features as $f)
        <div class="card card-hover p-6">
          <div class="flex items-center gap-3">
            <div class="icon-bubble icon-bubble-{{ $f['tone'] }}">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6 9 17l-5-5"/>
              </svg>
            </div>
            <p class="text-base font-bold text-slate-900">{{ $f['title'] }}</p>
          </div>
          <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $f['desc'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  <!-- WORKFLOW -->
  <section id="workflow" class="container-pad py-12">
    <div class="grid lg:grid-cols-2 gap-8 items-start">
      <div class="card p-6">
        <span class="badge badge-emerald">Alur kerja</span>
        <h3 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">Dari form ke tindak lanjut.</h3>
        <p class="mt-2 text-slate-600">
          Struktur aplikasi dibuat agar alur kerja mudah dipahami sejak pertama digunakan.
        </p>

        <div class="mt-6 grid gap-3">
          <div class="card-solid p-4 flex gap-3">
            <div class="icon-bubble icon-bubble-indigo">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14"/><path d="M5 12h14"/>
              </svg>
            </div>
            <div>
              <p class="font-semibold text-slate-900">1) Buat form</p>
              <p class="text-sm text-slate-600">Tentukan field dan validasi, lalu publikasi.</p>
            </div>
          </div>

          <div class="card-solid p-4 flex gap-3">
            <div class="icon-bubble icon-bubble-emerald">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 7h12l-1 14H7L6 7z"/><path d="M9 7a3 3 0 0 1 6 0"/>
              </svg>
            </div>
            <div>
              <p class="font-semibold text-slate-900">2) Kelola order</p>
              <p class="text-sm text-slate-600">Lihat daftar order dan akses detail dengan cepat.</p>
            </div>
          </div>

          <div class="card-solid p-4 flex gap-3">
            <div class="icon-bubble icon-bubble-rose">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/><path d="M12 7v6l4 2"/>
              </svg>
            </div>
            <div>
              <p class="font-semibold text-slate-900">3) Tindak lanjut</p>
              <p class="text-sm text-slate-600">Pantau sesi yang belum selesai dan lakukan follow-up.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <span class="badge badge-indigo">Modul</span>
        <h3 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">Struktur halaman yang jelas.</h3>
        <p class="mt-2 text-slate-600">
          Menu dan halaman disusun agar pengguna tidak perlu “mencari-cari” fungsi utama.
        </p>

        <div class="mt-6 grid sm:grid-cols-2 gap-3">
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Dashboard</p>
            <p class="mt-1 text-xs text-slate-600">Ringkasan dan shortcut</p>
          </div>
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Forms</p>
            <p class="mt-1 text-xs text-slate-600">Kelola form dan status</p>
          </div>
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Orders</p>
            <p class="mt-1 text-xs text-slate-600">Daftar dan detail order</p>
          </div>
          <div class="card-solid p-4">
            <p class="text-sm font-semibold text-slate-900">Abandoned</p>
            <p class="mt-1 text-xs text-slate-600">Sesi belum selesai</p>
          </div>
        </div>
      </div>
    </div>
  </section>


  

  <!-- ACCESS / SECURITY -->
  <section id="access" class="container-pad py-12">
    <div class="grid lg:grid-cols-3 gap-4">
      <div class="card p-6">
        <span class="badge badge-indigo">Akses</span>
        <p class="mt-3 text-xl font-extrabold text-slate-900">Autentikasi dan akses akun.</p>
        <p class="mt-2 text-sm text-slate-600">
          Halaman login dan registrasi dibuat sederhana, jelas, dan konsisten dengan tampilan utama.
        </p>
      </div>
      <div class="card p-6">
        <p class="text-lg font-extrabold text-slate-900">Validasi jelas</p>
        <p class="mt-2 text-sm text-slate-600">
          Pesan error ditampilkan dengan rapi agar pengguna segera tahu apa yang perlu diperbaiki.
        </p>
      </div>
      <div class="card p-6">
        <p class="text-lg font-extrabold text-slate-900">Pengalaman stabil</p>
        <p class="mt-2 text-sm text-slate-600">
          Layout dan komponen konsisten agar tidak terasa berpindah aplikasi saat berpindah halaman.
        </p>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="container-pad py-12">
    <div class="grid lg:grid-cols-2 gap-6">
      <div>
        <span class="badge badge-amber">FAQ</span>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Informasi tambahan</h2>
        <p class="mt-2 text-slate-600">
          Beberapa hal yang biasanya ditanyakan sebelum mulai menggunakan sistem.
        </p>
      </div>

      <div class="grid gap-3">
        <div class="card p-5">
          <p class="font-semibold text-slate-900">Apakah sistem ini cocok untuk penggunaan internal?</p>
          <p class="mt-2 text-sm text-slate-600">
            Cocok. Struktur halaman dan alur kerja dibuat netral untuk kebutuhan internal maupun eksternal.
          </p>
        </div>
       
        <div class="card p-5">
          <p class="font-semibold text-slate-900">Apakah tampilan dapat dikembangkan lagi?</p>
          <p class="mt-2 text-sm text-slate-600">
            Ya. Sistem komponen dan layout memungkinkan penambahan halaman dan fitur tanpa mengubah arah desain.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- FINAL CTA (NETRAL) -->
  <section class="container-pad pb-14">
    <div class="card p-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <h3 class="text-2xl font-extrabold tracking-tight text-slate-900">Masuk untuk mulai mengelola form dan order.</h3>
          <p class="mt-2 text-slate-600">
            Jika sudah memiliki akun, masuk dan lanjutkan pekerjaan. Jika belum, buat akun terlebih dahulu.
          </p>
        </div>
        <div class="flex gap-3">
          @auth
            <a class="btn-primary" href="{{ route('app.dashboard') }}">Buka Dashboard</a>
          @else
            <a class="btn-primary" href="{{ route('login') }}">Masuk</a>
            <a class="btn-outline" href="{{ route('register') }}">Buat Akun</a>
          @endauth
        </div>
      </div>
    </div>

    <footer class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-600">
      <div class="inline-flex items-center gap-2">
        <x-application-logo class="h-7 w-auto" />
      </div>
      <p>© {{ date('Y') }} {{ config('app.name', 'Form Order System') }}.</p>
    </footer>
  </section>
@endsection

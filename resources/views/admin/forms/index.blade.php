{{-- resources/views/admin/forms/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Form Saya
            </h2>
            <a
                href="{{ route('admin.forms.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Buat Form Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 rounded-md bg-green-50 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($forms->isEmpty())
                        <p class="text-gray-600">
                            Belum ada form. Klik <span class="font-semibold">"Buat Form Baru"</span> untuk membuat campaign pertama.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="px-3 py-2 text-left">Nama Form</th>
                                        <th class="px-3 py-2 text-left">Slug</th>
                                        <th class="px-3 py-2 text-left">Status</th>
                                        <th class="px-3 py-2 text-left">Embed Code</th>
                                        <th class="px-3 py-2 text-left">Dibuat</th>
                                        <th class="px-3 py-2 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($forms as $form)
                                        <tr class="border-b">
                                            <td class="px-3 py-2">
                                                <div class="font-semibold text-gray-900">{{ $form->name }}</div>
                                                @if ($form->description)
                                                    <div class="text-xs text-gray-500 line-clamp-1">
                                                        {{ $form->description }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-xs text-gray-600">
                                                {{ $form->slug }}
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs
                                                    {{ $form->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ $form->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-xs font-mono align-top">
                                                <div class="bg-gray-100 rounded p-2 break-all">
                                                    &lt;script src="{{ url('/embed/'.$form->embed_token.'.js') }}"&gt;&lt;/script&gt;
                                                </div>
                                                <p class="text-[10px] text-gray-500 mt-1">
                                                    Tempelkan script ini di landing page (Berdu, WordPress, dll).
                                                </p>
                                            </td>
                                            <td class="px-3 py-2 text-xs text-gray-600">
                                                {{ $form->created_at->format('d M Y H:i') }}
                                            </td>
                                            <td class="px-3 py-2 text-right text-xs space-x-3">
                                                 <a href="{{ route('admin.forms.design', $form) }}"
                   class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    Pengaturan
                </a>
    <a href="{{ route('admin.forms.fields.edit', $form) }}"
       class="text-indigo-600 hover:text-indigo-800">
        Kelola Field
    </a>
    <a href="{{ route('admin.forms.preview', $form) }}"
       class="text-gray-600 hover:text-gray-900">
        Preview
    </a>
</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $forms->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
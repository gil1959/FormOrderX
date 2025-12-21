<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormFieldController extends Controller
{
    // Halaman kelola field untuk 1 form
    public function edit(Form $form)
    {
        $this->authorizeForm($form);

        $fields = $form->fields()->orderBy('order')->get();

        // list asli (biar kompatibel sama view lama kalau ada yang masih pakai)
        $fieldTypes = ['text', 'textarea', 'number', 'tel', 'email', 'select'];

        // label manusia (buat UI awam)
        $fieldTypeLabels = [
            'text'     => 'Teks (1 baris)',
            'textarea' => 'Teks Panjang (alamat/catatan)',
            'number'   => 'Angka',
            'tel'      => 'No. HP / WhatsApp',
            'email'    => 'Email',
            'select'   => 'Pilihan (Dropdown)',
        ];

        return view('admin.forms.fields', compact('form', 'fields', 'fieldTypes', 'fieldTypeLabels'));
    }

    // Tambah field baru
    public function store(Request $request, Form $form)
    {
        $this->authorizeForm($form);

        $data = $request->validate([
            'label'     => ['required', 'string', 'max:255'],
            'name'      => ['required', 'string', 'max:255'],
            'type'      => ['required', 'string', 'in:text,textarea,number,tel,email,select'],
            'required'  => ['nullable', 'boolean'],
            'options'   => ['nullable', 'string'], // comma separated, untuk select
            'show_in_summary' => ['nullable', 'boolean'],
        ]);

        $order = ($form->fields()->max('order') ?? 0) + 1;

        $options = null;
        if ($data['type'] === 'select' && !empty($data['options'])) {
            $options = collect(explode(',', $data['options']))
                ->map(fn($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        $form->fields()->create([
            'label'     => $data['label'],
            'name'      => $data['name'],
            'type'      => $data['type'],
            'required'  => $data['required'] ?? false,
            'options'   => $options,
            'order'     => $order,
            'is_active' => true,
            // default: true (checkbox checked). Perlu migration tambah kolom show_in_summary.
            'show_in_summary' => $request->boolean('show_in_summary', true),
        ]);

        return back()->with('success', 'Field berhasil ditambahkan.');
    }

    // Hapus field
    public function destroy(Form $form, FormField $field)
    {
        $this->authorizeForm($form);

        if ($field->form_id !== $form->id) {
            abort(404);
        }

        $field->delete();

        return back()->with('success', 'Field berhasil dihapus.');
    }

    protected function authorizeForm(Form $form): void
    {
        // pastikan form memang milik user yang login
        abort_unless($form->user_id === Auth::id(), 403);
    }
}

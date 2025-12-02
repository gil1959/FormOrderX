<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormEmbedController extends Controller
{
    /**
     * Keluarkan file JS yang akan di-embed:
     * <script src="https://domainlu.com/embed/js_xxxxx.js"></script>
     */
    public function script(string $token, Request $request)
    {
        $form = Form::with(['fields' => function ($q) {
            $q->where('is_active', true)->orderBy('order');
        }])
            ->where('embed_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        // Ambil setting JSON dari kolom "settings"
        $settings = $form->settings ?? [];

        // Layout
        $layout = $settings['layout'] ?? [];
        $template   = $layout['template']   ?? 'right_sidebar'; // right_sidebar | left_sidebar | no_sidebar
        $background = $layout['background'] ?? 'white';         // white | soft_green | soft_beige | soft_gray

        // Tracking pixel
        $tracking = $settings['tracking'] ?? [];

        $fbIds    = $tracking['facebook_pixel_ids'] ?? [];
        $fbEvents = $tracking['facebook_events']    ?? [];
        $gtmIds   = $tracking['gtm_ids']            ?? [];
        $ttIds    = $tracking['tiktok_pixel_ids']   ?? [];

        // Button
        $button = $settings['button'] ?? [];
        $btnLabel = $button['label'] ?? 'KIRIM';
        $btnColor = $button['color'] ?? 'blue';   // blue | green | orange | red
        $btnShape = $button['shape'] ?? 'pill';   // square | rounded | pill

        // Product (gambar & garansi)
        $product = $settings['product'] ?? [];
        $showImage      = (bool)($product['show_image'] ?? false);
        $imageUrl       = $product['image_url'] ?? null;
        $showGuarantee  = (bool)($product['show_guarantee'] ?? false);
        $guaranteeLabel = $product['guarantee_label'] ?? '100% Jaminan Kepuasan';
        if ($imageUrl && ! Str::startsWith($imageUrl, ['http://', 'https://'])) {
            // contoh: '/storage/form-images/xxx.jpg' → 'https://domain-laravel.com/storage/form-images/xxx.jpg'
            $imageUrl = url($imageUrl);
        }

        // Variasi produk
        $variation = $settings['variation'] ?? [];
        $varEnabled = (bool)($variation['enabled'] ?? false);
        $varType    = $variation['type'] ?? 'radio';  // radio | dropdown
        $varLabel   = $variation['label'] ?? 'Pilih Varian';
        $varOptions = $variation['options'] ?? [];

        // Ubah field DB → struktur sederhana buat JS
        $fields = $form->fields->map(function ($field) {
            return [
                'label'    => $field->label,
                'name'     => $field->name,
                'type'     => $field->type,      // text, textarea, select, tel, email, number, etc
                'required' => (bool)$field->required,
                'options'  => $field->options ?? [],
            ];
        })->values();

        $fieldsJson    = json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $varOptionsJson = json_encode($varOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $fbIdsJson    = json_encode($fbIds, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $fbEventsJson = json_encode($fbEvents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $gtmIdsJson   = json_encode($gtmIds, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $ttIdsJson    = json_encode($ttIds, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


        $submitUrl  = url('/api/submit/' . $form->embed_token);
        $formId     = 'formorderx_' . $form->id;

        // kirim juga setting yang dibutuhkan ke JS
        $btnLabelJs      = e($btnLabel);
        $varLabelJs      = e($varLabel);
        $guaranteeLabelJs = e($guaranteeLabel);
        $titleJs         = e($form->name);

        $js = <<<JS
(function() {
    // ==== DATA DARI BACKEND (PHP → JS) ====
    var formId     = "{$formId}";
    var submitUrl  = "{$submitUrl}";
    var fields     = {$fieldsJson};
    var varEnabled = {$this->boolToJs($varEnabled)};
    var varType    = "{$varType}";
    var varLabel   = "{$varLabelJs}";
    var varOptions = {$varOptionsJson};

    var template   = "{$template}";
    var background = "{$background}";
    var btnLabel   = "{$btnLabelJs}";
    var btnColor   = "{$btnColor}";
    var btnShape   = "{$btnShape}";
    var showImage  = {$this->boolToJs($showImage)};
    var imageUrl   = {$this->nullableStringToJs($imageUrl)};
    var showGuarantee  = {$this->boolToJs($showGuarantee)};
    var guaranteeLabel = "{$guaranteeLabelJs}";
    var title = "{$titleJs}";
     var fbPixelIds   = {$fbIdsJson};
    var fbEvents     = {$fbEventsJson};
    var gtmIds       = {$gtmIdsJson};
    var tiktokIds    = {$ttIdsJson};

    // ==== MAPPING SETTINGS → CSS ====

    // Warna utama tombol
    function getPrimaryColor() {
        switch (btnColor) {
            case 'green':  return '#16a34a';
            case 'orange': return '#f97316';
            case 'red':    return '#dc2626';
            case 'blue':
            default:       return '#2563eb';
        }
    }

    // Background card
    function getBackgroundColor() {
        switch (background) {
            case 'soft_green': return '#ecfdf3';
            case 'soft_beige': return '#fffbeb';
            case 'soft_gray':  return '#f8fafc';
            case 'white':
            default:           return '#ffffff';
        }
    }

    // Radius tombol
    function getButtonRadius() {
        switch (btnShape) {
            case 'square':  return '6px';
            case 'rounded': return '12px';
            case 'pill':
            default:        return '9999px';
        }
    }

    // Lebar card berdasarkan template (kasar mirip OrderOnline)
    function getMaxWidth() {
        switch (template) {
            case 'no_sidebar':   return '640px';
            case 'left_sidebar':
            case 'right_sidebar':
            default:             return '480px';
        }
    }

    // Align form dalam container script
    function getContainerJustify() {
    switch (template) {
        case 'right_sidebar':
            // form di kanan, cocok ketika konten landing page di kiri
            return 'flex-end';

        case 'left_sidebar':
            // form di kiri, konten landing page di kanan
            return 'flex-start';

        case 'no_sidebar':
        default:
            // form di tengah
            return 'center';
    }
}
}

    // ==== BUILD HTML ====

    function buildFormHtml() {
        var primaryColor  = getPrimaryColor();
        var bgColor       = getBackgroundColor();
        var buttonRadius  = getButtonRadius();
        var maxWidth      = getMaxWidth();
        var justify       = getContainerJustify();

        var html = '';

        // wrapper flex supaya bisa center/left
        html += '<div style="width:100%;display:flex;justify-content:' + justify + ';margin:8px 0;">';

        // card utama
        html += '<form id="' + formId + '" ' +
            'style="max-width:' + maxWidth + ';width:100%;' +
            'background-color:' + bgColor + ';' +
            'border-radius:16px;' +
            'border:1px solid #e5e7eb;' +
            'padding:20px 18px;' +
            'box-shadow:0 10px 30px rgba(15,23,42,0.08);' +
            'font-family:system-ui,-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif;' +
            'font-size:14px;' +
            'color:#0f172a;">';

        // label garansi di atas (opsional)
        if (showGuarantee && guaranteeLabel) {
            html += '<div style="margin-bottom:10px;display:inline-flex;align-items:center;padding:4px 10px;border-radius:9999px;background-color:#ecfdf3;color:#166534;font-size:11px;font-weight:600;">';
            html += '<span style="margin-right:6px;">✓</span>' + guaranteeLabel + '</div>';
        }

        // judul
       html += '<div style="margin-bottom:12px;font-weight:700;font-size:16px;line-height:1.3;text-align:center;">'
    + title +
    '</div>';

        // gambar produk (opsional)
        if (showImage && imageUrl) {
            html += '<div style="margin-bottom:12px;">';
            html += '<img src="' + imageUrl + '" alt="" style="width:100%;border-radius:12px;object-fit:cover;">';
            html += '</div>';
        }

        // garis tipis
        html += '<div style="height:1px;background-color:#e5e7eb;margin:8px 0 14px 0;"></div>';

        // VARIASI PRODUK (kalau diaktifkan)
        if (varEnabled && Array.isArray(varOptions) && varOptions.length > 0) {
            html += '<div style="margin-bottom:12px;">';
            html += '<label style="display:block;margin-bottom:4px;font-weight:600;font-size:13px;">' + varLabel + '</label>';

            if (varType === 'dropdown') {
                html += '<select name="__variation" ' +
                    'style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;">';
                html += '<option value="">Pilih...</option>';
                varOptions.forEach(function(opt) {
                    if (!opt || !opt.label) return;
                    var val = opt.value || opt.label;
                    html += '<option value="' + escapeHtml(val) + '">' + escapeHtml(opt.label) + '</option>';
                });
                html += '</select>';
            } else {
                // radio
                varOptions.forEach(function(opt, idx) {
                    if (!opt || !opt.label) return;
                    var val = opt.value || opt.label;
                    var id  = formId + '_var_' + idx;
                    html += '<label style="display:flex;align-items:center;margin-bottom:4px;font-size:13px;cursor:pointer;">';
                    html += '<input type="radio" name="__variation" value="' + escapeHtml(val) + '" ' +
                        'style="margin-right:6px;" />';
                    html += '<span>' + escapeHtml(opt.label) + '</span>';
                    html += '</label>';
                });
            }

            html += '</div>';
        }

        // FIELD-FIELD FORM UTAMA
        fields.forEach(function(field) {
            html += '<div style="margin-bottom:12px;">';
            html += '<label style="display:block;margin-bottom:4px;font-weight:500;font-size:13px;">' +
                escapeHtml(field.label) +
                (field.required ? ' <span style="color:#dc2626">*</span>' : '') +
                '</label>';

            var requiredAttr = field.required ? 'required' : '';

            if (field.type === 'textarea') {
                html += '<textarea name="' + escapeAttr(field.name) + '" ' + requiredAttr +
                    ' style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;min-height:70px;"></textarea>';
            } else if (field.type === 'select') {
                html += '<select name="' + escapeAttr(field.name) + '" ' + requiredAttr +
                    ' style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;">';
                html += '<option value="">Pilih...</option>';
                (field.options || []).forEach(function(opt) {
                    html += '<option value="' + escapeHtml(opt) + '">' + escapeHtml(opt) + '</option>';
                });
                html += '</select>';
            } else {
                var inputType = (field.type === 'tel' || field.type === 'email' || field.type === 'number')
                    ? field.type
                    : 'text';

                html += '<input type="' + inputType + '" name="' + escapeAttr(field.name) + '" ' + requiredAttr +
                    ' style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;" />';
            }

            html += '</div>';
        });

        // tombol submit
       html += '<div style="width:100%;display:flex;justify-content:center;margin-top:10px;">';
html += '<button type="submit" ' +
    'style="display:inline-flex;align-items:center;justify-content:center;' +
    'padding:10px 20px;' +
    'border-radius:' + buttonRadius + ';' +
    'border:none;' +
    'background-color:' + primaryColor + ';' +
    'color:#ffffff;' +
    'font-weight:600;' +
    'font-size:13px;' +
    'cursor:pointer;' +
    'box-shadow:0 6px 18px rgba(15,23,42,0.25);' +
    'transition:transform 0.05s ease;">' +
    escapeHtml(btnLabel) +
    '</button>';
html += '</div>';

        html += '<div id="' + formId + '_message" style="margin-top:8px;font-size:12px;"></div>';

        html += '</form>';
        html += '</div>'; // end wrapper

        return html;
    }

    // Escape helper
    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttr(str) {
        return escapeHtml(str).replace(/"/g, '&quot;');
    }

        // ==== TRACKING HELPER ====
    function fireTracking() {
        // Facebook Pixel
        try {
            if (Array.isArray(fbPixelIds) && fbPixelIds.length && typeof fbq === 'function') {
                // init semua pixel ID
                fbPixelIds.forEach(function(id) {
                    if (!id) return;
                    fbq('init', id);
                });

                // pakai event custom dari setting, fallback ke "Lead"
                var eventName = (Array.isArray(fbEvents) && fbEvents.length && fbEvents[0])
                    ? fbEvents[0]
                    : 'Lead';

                fbq('track', eventName);
            }
        } catch (e) {
            // diam saja kalau error
        }

        // TikTok Pixel
        try {
            if (Array.isArray(tiktokIds) && tiktokIds.length && window.ttq && typeof ttq.track === 'function') {
                // contoh event: "SubmitForm"
                ttq.track('SubmitForm');
            }
        } catch (e) {}

        // Google Tag Manager (dataLayer)
        try {
            if (Array.isArray(gtmIds) && gtmIds.length && Array.isArray(window.dataLayer)) {
                window.dataLayer.push({
                    'event': 'form_order_submit',
                    'form_id': formId,
                    'gtm_ids': gtmIds
                });
            }
        } catch (e) {}
    }


    // ==== SISIPKAN FORM DI POSISI SCRIPT ====
    document.write(buildFormHtml());

    // ==== HANDLER SUBMIT ====
    window.addEventListener('load', function() {
        var form   = document.getElementById(formId);
        if (!form) return;

        var msgBox = document.getElementById(formId + '_message');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!msgBox) return;
            msgBox.style.color = '#4b5563';
            msgBox.textContent = 'Mengirim...';

            var formData = new FormData(form);
            var dataObj  = {};

            formData.forEach(function(value, key) {
                dataObj[key] = value;
            });

            // kirim juga info source_url
            var payload = {
                data: dataObj,
                source_url: window.location.href
            };

            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(function(res) { return res.json(); })
            .then(function(json) {
    if (json && json.success) {
        msgBox.style.color = '#16a34a';
        msgBox.textContent = json.message || 'Terima kasih, data berhasil dikirim.';
        form.reset();

        // 🔥 kirim event ke pixel / GTM
        fireTracking();
    } else {
        msgBox.style.color = '#dc2626';
        msgBox.textContent = (json && json.message) || 'Terjadi kesalahan. Silakan coba lagi.';
    }
})
            .catch(function() {
                msgBox.style.color = '#dc2626';
                msgBox.textContent = 'Gagal mengirim. Periksa koneksi Anda.';
            });
        });
    });
})();
JS;

        return response($js, 200)->header('Content-Type', 'application/javascript');
    }

    /**
     * Endpoint yang dipanggil embed form saat submit.
     */
    public function submit(string $token, Request $request)
    {
        $form = Form::where('embed_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'data'       => ['required', 'array'],
            'source_url' => ['nullable', 'string', 'max:2048'],
        ]);

        Submission::create([
            'form_id'      => $form->id,
            'user_id'      => $form->user_id,
            'status'       => 'pending',
            'total_price'  => null,
            'data'         => $validated['data'],
            'source_url'   => $validated['source_url'] ?? null,
            'client_ip'    => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'is_spam'      => false,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih, pesanan Anda sudah kami terima.',
        ]);
    }

    // Helper kecil buat convert boolean/null ke JS
    private function boolToJs(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    private function nullableStringToJs(?string $value): string
    {
        if ($value === null || $value === '') {
            return 'null';
        }
        // amanin quote
        $escaped = addslashes($value);
        return '"' . $escaped . '"';
    }
}

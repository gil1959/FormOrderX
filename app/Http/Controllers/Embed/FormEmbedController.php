<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Support\Str;

class FormEmbedController extends Controller
{
    public function script(string $token)
    {
        $form = Form::with(['fields' => function ($q) {
            $q->where('is_active', true)->orderBy('order');
        }])
            ->where('embed_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $settings = $form->settings ?? [];

        // Layout
        $layout       = $settings['layout'] ?? [];
        $template     = $layout['template'] ?? 'right_sidebar';
        $background   = $layout['background'] ?? 'white';

        // Product
        $product = $settings['product'] ?? [];
        $showImage      = (bool)($product['show_image'] ?? false);
        $imageUrl       = $product['image_url'] ?? null;
        $showGuarantee  = (bool)($product['show_guarantee'] ?? false);
        $guaranteeLabel = $product['guarantee_label'] ?? '100% Jaminan Kepuasan';

        if ($imageUrl && !Str::startsWith($imageUrl, ['http://', 'https://'])) {
            $imageUrl = url($imageUrl);
        }

        // Button
        $button = $settings['button'] ?? [];
        $btnLabel = $button['label'] ?? 'KIRIM';
        $btnColor = $button['color'] ?? 'blue';
        $btnShape = $button['shape'] ?? 'pill';

        // Variasi
        $variation = $settings['variation'] ?? [];
        $varEnabled = (bool)($variation['enabled'] ?? false);
        $varType    = $variation['type'] ?? 'radio';
        $varLabel   = $variation['label'] ?? 'Pilih Varian';
        $varOptions = $variation['options'] ?? [];

        // Base price
        $basePrice = (float)($form->base_price ?? 0);

        // Field list
        $fields = $form->fields->map(function ($f) {
            return [
                'label'           => $f->label,
                'name'            => $f->name,
                'type'            => $f->type,
                'required'        => (bool)$f->required,
                'options'         => $f->options ?? [],
                // kalau kolom belum ada, default true biar gak nge-break
                'show_in_summary' => (bool)($f->show_in_summary ?? true),
            ];
        });

        $fieldsJson      = json_encode($fields, JSON_UNESCAPED_UNICODE);
        $varOptionsJson  = json_encode($varOptions, JSON_UNESCAPED_UNICODE);
        $basePriceJson   = json_encode($basePrice);

        $submitUrl    = url('/api/submit/' . $form->embed_token);
        $abandonedUrl = url('/api/abandoned/' . $form->embed_token . '/touch');
        $nonceUrl     = url('/api/embed/' . $form->embed_token . '/nonce');
        $addressSuggestUrl = url('/api/address-suggest');

        $formId       = 'formx_' . $form->id;

        // Escape teks
        $titleJs          = e($form->name);
        $btnLabelJs       = e($btnLabel);
        $guaranteeJs      = e($guaranteeLabel);
        $varLabelJs       = e($varLabel);

        $js = <<<JS
(function(){

    var formId       = "{$formId}";
    var submitUrl    = "{$submitUrl}";
    var nonceUrl     = "{$nonceUrl}";

    var abandonedUrl = "{$abandonedUrl}";
    var addressSuggestUrl = "{$addressSuggestUrl}";


    var title      = "{$titleJs}";
    var fields     = {$fieldsJson};

    var basePrice  = {$basePriceJson};

    var showImage  = {$this->boolToJs($showImage)};
    var imageUrl   = {$this->nullableStringToJs($imageUrl)};
    var showGuarantee = {$this->boolToJs($showGuarantee)};
    var guaranteeLabel = "{$guaranteeJs}";

    var btnLabel   = "{$btnLabelJs}";
    var btnColor   = "{$btnColor}";
    var btnShape   = "{$btnShape}";

    var background = "{$background}";
    var template   = "{$template}";

    var varEnabled = {$this->boolToJs($varEnabled)};
    var varType    = "{$varType}";
    var varLabel   = "{$varLabelJs}";
    var varOptions = {$varOptionsJson};
    // ================================
// Anti-spam: nonce single-use (server issued)
// ================================
var _nonceCache = null;
var _nonceFetching = null;

function fetchNonce(force){
    force = !!force;
    if (!force && _nonceCache) return Promise.resolve(_nonceCache);
    if (_nonceFetching) return _nonceFetching;

    _nonceFetching = fetch(nonceUrl, { method: 'GET' })
        .then(function(r){ return r.json().then(function(j){ return { ok: r.ok, json: j }; }); })
        .then(function(res){
            _nonceFetching = null;
            if (res.ok && res.json && res.json.nonce) {
                _nonceCache = String(res.json.nonce);
                return _nonceCache;
            }
            return '';
        })
        .catch(function(){
            _nonceFetching = null;
            return '';
        });

    return _nonceFetching;
}


    function getPrimaryColor() {
        switch (btnColor) {
            case 'green': return '#16a34a';
            case 'orange': return '#f97316';
            case 'red': return '#dc2626';
            default: return '#2563eb';
        }
    }

    function getBackgroundColor() {
        switch (background) {
            case 'soft_green': return '#ecfdf3';
            case 'soft_beige': return '#fffbeb';
            case 'soft_gray': return '#f8fafc';
            default: return '#ffffff';
        }
    }

    function getButtonRadius() {
        switch (btnShape) {
            case 'square': return '6px';
            case 'rounded': return '12px';
            default: return '9999px';
        }
    }
    var radius = getButtonRadius();

    function dividerVertical(){
    return '<div style="width:1px;background:#e5e7eb;align-self:stretch;"></div>';
}
function dividerHorizontal(){
    return '<div style="height:1px;background:#e5e7eb;margin:12px 0;"></div>';
}

    function formatRupiah(n){
        n = Number(n || 0);
        var s = Math.round(n).toString();
        return 'Rp ' + s.replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.');
    }

    // Wrapper responsive untuk sidebar mode
    function layoutWrapper(open){
        if (!open) return '</div>';
        return '<div style="width:100%;display:flex;gap:18px;flex-wrap:wrap;justify-content:center;margin:10px 0;">';
    }

    function columnLeft(content){
        return '<div style="flex:1;min-width:260px;max-width:380px;display:flex;flex-direction:column;align-items:center;">'
            + content + '</div>';
    }

    function columnRight(content){
        return '<div style="flex:1;min-width:260px;max-width:380px;">'
            + content + '</div>';
    }

    function guaranteeBadge(){
        if (!showGuarantee || !guaranteeLabel) return '';
        return '<div style="margin-top:8px;text-align:center;">'
            + '<span style="display:inline-block;padding:6px 10px;border-radius:9999px;background:#ECFDF5;border:1px solid #A7F3D0;color:#047857;font-size:12px;font-weight:600;">'
            + '✓ ' + guaranteeLabel
            + '</span>'
            + '</div>';
    }

    function imageBlock(){
        var c = '';
        if (showImage && imageUrl) {
            c += '<img src=\"'+imageUrl+'\" style=\"width:100%;border-radius:12px;margin-bottom:12px;display:block;\">';
        }
        // guarantee harus bisa tampil walau tanpa image
        c += guaranteeBadge();
        return c;
    }

    function optionLabelWithPrice(o){
        if (!o) return '';
        var p = (o.price !== undefined && o.price !== null && o.price !== '' && !isNaN(Number(o.price))) ? Number(o.price) : 0;
        return o.label + (p > 0 ? (' ('+formatRupiah(p)+')') : '');
    }

function getSessionKey(){
    try {
        var keyName = 'formx_session_' + formId;
        var existing = localStorage.getItem(keyName);
        if (existing) return existing;

        var newKey = 'sx_' + Math.random().toString(36).slice(2) + '_' + Date.now();
        localStorage.setItem(keyName, newKey);
        return newKey;
    } catch(e) {
        // fallback kalau localStorage diblok (incognito/setting ketat)
        return 'sx_' + Math.random().toString(36).slice(2) + '_' + Date.now();
    }
}

function debounce(fn, wait){
    var t = null;
    return function(){
        var ctx = this, args = arguments;
        clearTimeout(t);
        t = setTimeout(function(){ fn.apply(ctx, args); }, wait);
    };
}

function touchAbandoned(form){
    // kumpulin data form
    var fd = new FormData(form);
    var obj = {};
    fd.forEach(function(v,k){ obj[k]=v; });

    fetch(abandonedUrl, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            session_key: getSessionKey(),
            data: obj,
            source_url: window.location.href
        })
    }).catch(function(){ /* silent */ });
}

    function buildInnerFormHtml(){
    // ============ CARD WRAPPER (border form) ============
    var cardOpen = '<div style="position:relative;overflow:visible;background:'+getBackgroundColor()+';border:1px solid #e5e7eb;border-radius:16px;padding:18px;box-shadow:0 8px 26px rgba(0,0,0,0.05);font-family:sans-serif;">';
    var cardClose = '</div>';

    // ============ BODY (isi form tanpa card) ============
    function buildBody(){
        var body = '';

        body += '<div style="font-size:17px;font-weight:700;text-align:center;margin-bottom:14px;">'+title+'</div>';

        // --- anti-spam hidden signals (no UX impact) ---
body += '<input type="text" name="_hp" value="" autocomplete="off" tabindex="-1" aria-hidden="true" '
    + 'style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">';
body += '<input type="hidden" name="_ts" value="'+Date.now()+'">';


        // Variasi
        if (varEnabled && varOptions.length > 0) {
            body += '<label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px;">'+varLabel+'</label>';

            if (varType === 'dropdown') {
                body += '<select name="__variation" '+(varEnabled?'required':'')+' style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;margin-bottom:12px;">';
                body += '<option value="">Pilih...</option>';
                varOptions.forEach(function(o){
                    body += '<option value="'+o.value+'">'+optionLabelWithPrice(o)+'</option>';
                });
                body += '</select>';
            } else {
                varOptions.forEach(function(o){
                    body += '<label style="display:flex;align-items:center;margin-bottom:6px;">';
                    body += '<input type="radio" name="__variation" value="'+o.value+'" '+(varEnabled?'required':'')+' style="margin-right:8px;">'+optionLabelWithPrice(o);
                    body += '</label>';
                });
                body += '<div style="height:4px;"></div>';
            }

            body += '<div style="height:1px;background:#e5e7eb;margin:12px 0;"></div>';
        }

        // Fields
        fields.forEach(function(f){
            body += '<label style="font-size:13px;font-weight:500;margin-bottom:4px;display:block;">'
                + f.label + (f.required ? ' <span style="color:red">*</span>' : '')
                + '</label>';

           if (f.type === 'textarea') {
    body += '<textarea name="'+f.name+'" '+(f.required?'required':'')+' style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:12px;min-height:70px;"></textarea>';
}
else if (f.type === 'select') {
    body += '<select name="'+f.name+'" '+(f.required?'required':'')+' style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:12px;">';
    body += '<option value="">Pilih...</option>';
    (f.options || []).forEach(function(o){
        body += '<option value="'+String(o).replace(/"/g,'&quot;')+'">'+o+'</option>';
    });
    body += '</select>';
}
else if (f.type === 'address_suggest') {
    var wrapId = formId + '_as_wrap_' + f.name;
    var listId = formId + '_as_list_' + f.name;

    body += '<div id="'+wrapId+'" style="position:relative;margin-bottom:12px;overflow:visible;">';

body += '<input type="text" autocomplete="off" name="'+f.name+'" '+(f.required?'required':'')+' ' +
        'style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;" ' +
        'placeholder="Ketik minimal 3 huruf...">';
body += '<input type="hidden" name="'+f.name+'_postal">';
body += '<div id="'+listId+'" style="display:none;position:absolute;left:0;right:0;top:calc(100% + 6px);z-index:999999;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 12px 28px rgba(0,0,0,.12);max-height:260px;overflow:auto;-webkit-overflow-scrolling:touch;"></div>';

body += '</div>';

}
else {
    var inputType = (f.type==='number'||f.type==='email'||f.type==='tel') ? f.type : 'text';
    body += '<input type="'+inputType+'" name="'+f.name+'" '+(f.required?'required':'')+' style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:12px;">';
}

        });

        // Ringkasan pemesanan
        body += '<div id="'+formId+'_summary_box" style="margin:14px 0;padding:12px;border:1px dashed #e5e7eb;border-radius:12px;background:#ffffff;">';
        body += '<div style="font-weight:700;font-size:13px;margin-bottom:8px;color:#0f172a;">Ringkasan Pemesanan</div>';
        body += '<div id="'+formId+'_summary_items" style="font-size:13px;color:#334155;line-height:1.45;"></div>';
        body += '<div style="height:10px;"></div>';
        body += '<div style="display:flex;justify-content:space-between;font-weight:800;color:#0f172a;">';
        body += '<span>Total</span><span id="'+formId+'_summary_total">'+formatRupiah(0)+'</span>';
        body += '</div>';
        body += '</div>';

        // Button
        // Button
body += '<button id="'+formId+'_submit" type="submit" style="width:100%;padding:14px 16px;border:0;border-radius:'+radius+';background:'+getPrimaryColor()+';color:white;font-weight:600;cursor:pointer;">'+btnLabel+'</button>';

        body += '<div id="'+formId+'_msg" style="margin-top:8px;font-size:12px;"></div>';

        return body;
    }

    // ============ LAYOUT CONTENT (gambar + body) ============
    var imgHtml = imageBlock();
    var bodyHtml = buildBody();
    var content = '';

    // RIGHT SIDEBAR: gambar kiri, body kanan (semua di DALAM card)

if (template === 'right_sidebar') {
    // PENTING: stretch biar tinggi kolom kiri ngikut kolom kanan
    content += '<div style="display:flex;gap:16px;align-items:stretch;flex-wrap:wrap;">';

    if (imgHtml.trim() !== '') {
        // kolom kiri jadi flex container buat center vertikal
        content += ''
          + '<div style="flex:1;min-width:220px;max-width:320px;display:flex;align-items:center;justify-content:center;">'
          +   '<div style="width:100%;">' + imgHtml + '</div>'
          + '</div>';

        content += dividerVertical();
    }

    // kolom kanan
    content += '<div style="flex:2;min-width:260px;">' + bodyHtml + '</div>';
    content += '</div>';

    return cardOpen + content + cardClose;
}

    // LEFT SIDEBAR: body kiri, gambar kanan (semua di DALAM card)
if (template === 'left_sidebar') {
    // PENTING: stretch biar tinggi kolom kanan ngikut kolom kiri (form)
    content += '<div style="display:flex;gap:16px;align-items:stretch;flex-wrap:wrap;">';

    // kolom kiri (form/body)
    content += '<div style="flex:2;min-width:260px;">' + bodyHtml + '</div>';

    // kolom kanan (gambar + badge) center vertikal
    if (imgHtml.trim() !== '') {
        content += dividerVertical();
        content += ''
          + '<div style="flex:1;min-width:220px;max-width:320px;display:flex;align-items:center;justify-content:center;">'
          +   '<div style="width:100%;">' + imgHtml + '</div>'
          + '</div>';
    }

    content += '</div>';
    return cardOpen + content + cardClose;
}

    // NO SIDEBAR: gambar atas, divider horizontal, body bawah (semua di DALAM card)
    if (imgHtml.trim() !== '') {
        content += '<div>' + imgHtml + '</div>';
        content += dividerHorizontal();
    }
    content += bodyHtml;

    return cardOpen + content + cardClose;
}

    function buildFormHtml(){
    // buildInnerFormHtml() sekarang sudah termasuk card + layout gambar di dalamnya
    var formBox = '<form id="'+formId+'">' + buildInnerFormHtml() + '</form>';

    // centering wrapper: biar rapi di WP / builder
    var html = '';
    html += '<div style="max-width:820px;margin:10px auto;padding:0 10px;box-sizing:border-box;">';
    html += formBox;
    html += '</div>';
    return html;
}

    function getSelectedVariation(form){
        if (!(varEnabled && varOptions && varOptions.length)) return null;
        var val = null;
        if (varType === 'dropdown') {
            var sel = form.querySelector('select[name=\"__variation\"]');
            val = sel ? sel.value : null;
        } else {
            var r = form.querySelector('input[name=\"__variation\"]:checked');
            val = r ? r.value : null;
        }
        if (!val) return null;
        for (var i=0;i<varOptions.length;i++) {
            if (varOptions[i] && varOptions[i].value === val) return varOptions[i];
        }
        return null;
    }

    function buildSummaryAndTotal(form){
        var itemsEl = document.getElementById(formId+'_summary_items');
        var totalEl = document.getElementById(formId+'_summary_total');
        if (!itemsEl || !totalEl || !form) return { total: Number(basePrice||0), summary: [] };

        var summary = [];
        var total = 0;

        // Produk
        summary.push({ label: 'Produk', value: title });

        // Variasi
        var v = getSelectedVariation(form);
        if (v) {
            var p = (v.price !== undefined && v.price !== null && v.price !== '' && !isNaN(Number(v.price))) ? Number(v.price) : 0;
            var vText = v.label + (p > 0 ? (' ('+formatRupiah(p)+')') : '');
            summary.push({ label: varLabel, value: vText });
            total = p;
        }

        // Field yang ditandai tampil di ringkasan
        fields.forEach(function(f){
            if (!f.show_in_summary) return;
            var el = form.querySelector('[name=\"'+f.name+'\"]');
            if (!el) return;

            var val = '';
            if (el.tagName === 'SELECT') {
                val = (el.value || '').toString().trim();
                if (!val) return;
            } else {
                val = (el.value || '').toString().trim();
                if (!val) return;
            }

            summary.push({ label: f.label, value: val });
        });

        // Render
       itemsEl.innerHTML = summary.map(function(x){
    return '<div style=\"display:flex;gap:8px;margin-bottom:4px;align-items:flex-start;\">'
        + '<div style=\"min-width:120px;font-weight:700;\">'+x.label+':</div>'
        + '<div style=\"flex:1;min-width:0;overflow-wrap:anywhere;word-break:break-word;\">'
        +   String(x.value).replace(/</g,'&lt;').replace(/>/g,'&gt;')
        + '</div>'
        + '</div>';
}).join('');

        totalEl.textContent = formatRupiah(total);
        return { total: total, summary: summary };
    }
function attachAddressSuggest(form){
    if (!form) return;

    // cari semua field bertipe address_suggest
    fields.forEach(function(f){
        if (f.type !== 'address_suggest') return;

        var input = form.querySelector('input[name="'+f.name+'"]');
        if (!input) return;

        var listId = formId + '_as_list_' + f.name;
        var listEl = document.getElementById(listId);
        if (!listEl) return;

        var lastQ = '';
        var hideTimer = null;

        function hideList(){
            listEl.style.display = 'none';
            listEl.innerHTML = '';
        }

        function renderItems(items){
            if (!items || !items.length) {
                hideList();
                return;
            }
            listEl.innerHTML = items.map(function(it){
    var safe = String(it.label || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    var postalAttr = String(it.postal || '').replace(/"/g,'&quot;');

    return '<div data-value="'+safe.replace(/"/g,'&quot;')+'" ' +
           'data-postal="'+postalAttr+'" ' +
           'style="padding:10px 12px;cursor:pointer;font-size:13px;line-height:1.35;color:#0f172a;background:#ffffff;border-bottom:1px solid #f1f5f9;" ' +
           'onmouseover="this.style.background=\'#f8fafc\'" ' +
           'onmouseout="this.style.background=\'#ffffff\'">' +
           safe +
           '</div>';
}).join('') +
'<div style="padding:8px 12px;font-size:11px;color:#64748b;background:#ffffff;position:sticky;bottom:0;border-top:1px solid #f1f5f9;">Klik untuk pilih</div>';

            listEl.style.display = 'block';
        }

        var doSearch = debounce(function(){
            var q = (input.value || '').trim();
            if (q.length < 3) { hideList(); return; }
            if (q === lastQ) return;
            lastQ = q;

            fetch(addressSuggestUrl + '?q=' + encodeURIComponent(q) + '&limit=8', {
                method: 'GET'
            })
            .then(function(r){ return r.json(); })
            .then(function(j){
                renderItems((j && j.items) ? j.items : []);
            })
            .catch(function(){
                hideList();
            });
        }, 250);

        input.addEventListener('input', function(){
            doSearch();
        });

        // click select
        listEl.addEventListener('mousedown', function(e){
            // mousedown (bukan click) biar gak keburu blur
            var target = e.target;
            if (!target) return;
           var val = target.getAttribute('data-value');
if (!val) return;

input.value = val;

// TAMBAH INI
var postal = target.getAttribute('data-postal') || '';
var postalEl = form.querySelector('input[name="'+f.name+'_postal"]');
if (postalEl) postalEl.value = postal;

hideList();


            // trigger summary refresh
            try { input.dispatchEvent(new Event('input', {bubbles:true})); } catch(e){}
        });

        // blur hide (delay dikit supaya mousedown kepick)
        input.addEventListener('blur', function(){
            hideTimer = setTimeout(hideList, 150);
        });
        input.addEventListener('focus', function(){
            if (hideTimer) clearTimeout(hideTimer);
            // optional: kalau value >=3, munculin lagi
            if ((input.value||'').trim().length >= 3) doSearch();
        });
    });
}

    function attachBehaviors(){
        var form = document.getElementById(formId);
        var msg  = document.getElementById(formId+'_msg');
        var btn = document.getElementById(formId + '_submit');
var isSubmitting = false;

        if (!form) return;
attachAddressSuggest(form);
        // initial
        buildSummaryAndTotal(form);

        var debouncedTouch = debounce(function(){
            touchAbandoned(form);
        }, 1200);

        form.addEventListener('input', function(){
            buildSummaryAndTotal(form);
            debouncedTouch();
        });

        form.addEventListener('change', function(){
            buildSummaryAndTotal(form);
            debouncedTouch();
        });

        form.addEventListener('submit', function(e){
    e.preventDefault();

    // guard anti double submit
    if (isSubmitting) return;
    isSubmitting = true;

    // lock button
    if (btn) {
        btn.disabled = true;
        btn.dataset.originalText = btn.textContent || btn.innerText || '';
        btn.textContent = 'Memproses...';
        btn.style.background = '#cbd5e1';      // abu-abu
        btn.style.color = '#475569';
        btn.style.cursor = 'not-allowed';
        btn.style.opacity = '0.9';
    }

    if (msg) {
        msg.textContent = 'Mengirim...';
        msg.style.color = '#555';
    }

    var fd = new FormData(form);
var obj = {};
fd.forEach(function(v,k){ obj[k]=v; });

// ambil anti-spam signals, lalu buang dari payload data utama
var hp = (obj && obj._hp) ? String(obj._hp) : '';
var ts = (obj && obj._ts) ? String(obj._ts) : '';

if (obj) {
  delete obj._hp;
  delete obj._ts;
}

// fallback kalau _ts kosong (misal form di-cache aneh)
if (!ts) ts = String(Date.now());

var calc = buildSummaryAndTotal(form);

// nonce itu single-use, jadi setiap submit wajib ambil nonce baru
fetchNonce(true)
.then(function(nonce){
    // ambil hidden anti-spam signal, lalu buang dari data utama
    var hp = obj && obj._hp ? String(obj._hp) : '';
    var ts = obj && obj._ts ? String(obj._ts) : '';
    if (obj) { delete obj._hp; delete obj._ts; }

    return fetch(submitUrl, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            data: obj,
            meta: {
                nonce: String(nonce || ''),
                hp: hp,
                ts: ts
            },
            source_url: window.location.href,
            total_price: calc.total,
            summary: calc.summary,
            session_key: getSessionKey()
        })
    });
})


    .then(function(r){ return r.json().then(function(j){ return { ok: r.ok, json: j }; }); })
    .then(function(res){
        var j = res.json;

        if (res.ok) {
            // kalau backend kirim redirect_url, redirect
            if (j.redirect_url) {
                window.location.href = j.redirect_url;
                return;
            }

            if (msg) {
                msg.style.color = '#16a34a';
                msg.textContent = j.message || 'Berhasil.';
            }

            form.reset();
            buildSummaryAndTotal(form);

            // sukses: tombol tetap disabled (sesuai requirement lu)
            if (btn) {
                btn.textContent = 'Terkirim';
                // tetap abu-abu & disabled
            }

        } else {
            // gagal: balikin tombol
            if (msg) {
                msg.style.color = 'red';
                msg.textContent = (j && j.message) ? j.message : 'Gagal mengirim.';
            }

            isSubmitting = false;
            if (btn) {
                btn.disabled = false;
                btn.textContent = btn.dataset.originalText || 'KIRIM';
                btn.style.background = getPrimaryColor();
                btn.style.color = 'white';
                btn.style.cursor = 'pointer';
                btn.style.opacity = '1';
            }
        }
    })
    .catch(function(){
        if (msg) {
            msg.style.color = 'red';
            msg.textContent = 'Gagal mengirim.';
        }

        // gagal network: balikin tombol
        isSubmitting = false;
        if (btn) {
            btn.disabled = false;
            btn.textContent = btn.dataset.originalText || 'KIRIM';
            btn.style.background = getPrimaryColor();
            btn.style.color = 'white';
            btn.style.cursor = 'pointer';
            btn.style.opacity = '1';
        }
    });
});

    }

    // Render: aman untuk builder (kalau document.write masih boleh, pakai itu)
    function render(){
        var html = buildFormHtml();

        // Kalau masih loading, document.write paling kompatibel untuk banyak builder
        if (document.readyState === 'loading') {
            document.write(html);
            return;
        }

        // Kalau sudah selesai loading, sisipkan sebelum script tag ini
        var containerId = formId + '_container';
        var el = document.getElementById(containerId);
        if (!el) {
            el = document.createElement('div');
            el.id = containerId;
            el.style.width = '100%';
            el.style.boxSizing = 'border-box';

            if (document.currentScript && document.currentScript.parentNode) {
                document.currentScript.parentNode.insertBefore(el, document.currentScript);
            } else {
                document.body.appendChild(el);
            }
        }
        el.innerHTML = html;
    }

    render();

    // pastikan behavior kebinding setelah DOM ada
    window.addEventListener('load', attachBehaviors);

})();
JS;

        return response($js, 200)->header('Content-Type', 'application/javascript');
    }

    private function boolToJs(bool $v)
    {
        return $v ? 'true' : 'false';
    }

    private function nullableStringToJs(?string $v)
    {
        return $v ? '"' . addslashes($v) . '"' : 'null';
    }
}

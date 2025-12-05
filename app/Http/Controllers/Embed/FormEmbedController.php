<?php

namespace App\Http\Controllers\Embed;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Http\Request;
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

        if ($imageUrl && ! Str::startsWith($imageUrl, ['http://', 'https://'])) {
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

        // Field list
        $fields = $form->fields->map(function ($f) {
            return [
                'label'    => $f->label,
                'name'     => $f->name,
                'type'     => $f->type,
                'required' => (bool)$f->required,
                'options'  => $f->options ?? [],
            ];
        });

        $fieldsJson      = json_encode($fields, JSON_UNESCAPED_UNICODE);
        $varOptionsJson  = json_encode($varOptions, JSON_UNESCAPED_UNICODE);

        $submitUrl = url('/api/submit/' . $form->embed_token);
        $formId    = 'formx_' . $form->id;

        // Escape teks
        $titleJs          = e($form->name);
        $btnLabelJs       = e($btnLabel);
        $guaranteeJs      = e($guaranteeLabel);
        $varLabelJs       = e($varLabel);

        $js = <<<JS
(function(){

    var formId     = "{$formId}";
    var submitUrl  = "{$submitUrl}";
    var title      = "{$titleJs}";
    var fields     = {$fieldsJson};

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

    // SIDEBAR MODE C → RESPONSIVE
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

    function buildFormHtml(){

        var html = layoutWrapper(true);

        var imgBlock = '';
        if (showImage && imageUrl) {
            imgBlock += '<img src="'+imageUrl+'" style="width:100%;border-radius:12px;margin-bottom:12px;">';
            if (showGuarantee) {
                imgBlock += '<div style="padding:6px 10px;border-radius:9999px;background:#ecfdf3;color:#166534;font-size:12px;font-weight:600;display:inline-block;">✓ '+guaranteeLabel+'</div>';
            }
        }

        var formHtml = '<div style="background:'+getBackgroundColor()+';border:1px solid #e5e7eb;border-radius:16px;padding:18px;box-shadow:0 8px 26px rgba(0,0,0,0.05);font-family:sans-serif;">';
        formHtml += '<div style="font-size:17px;font-weight:700;text-align:center;margin-bottom:14px;">'+title+'</div>';

        // variasi
        if (varEnabled && varOptions.length > 0) {
            formHtml += '<label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px;">'+varLabel+'</label>';
            if (varType === 'dropdown') {
                formHtml += '<select name="__variation" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;margin-bottom:12px;">';
                formHtml += '<option value="">Pilih...</option>';
                varOptions.forEach(function(o){
                    formHtml += '<option value="'+o.value+'">'+o.label+'</option>';
                });
                formHtml += '</select>';
            } else {
                varOptions.forEach(function(o,i){
                    formHtml += '<label style="display:flex;align-items:center;margin-bottom:4px;">';
                    formHtml += '<input type="radio" name="__variation" value="'+o.value+'" style="margin-right:6px;">'+o.label;
                    formHtml += '</label>';
                });
            }
            formHtml += '<div style="height:1px;background:#e5e7eb;margin:12px 0;"></div>';
        }

        // fields
        fields.forEach(function(f){
            formHtml += '<label style="font-size:13px;font-weight:500;margin-bottom:4px;display:block;">'
                + f.label + (f.required ? ' <span style="color:red">*</span>' : '') 
                + '</label>';

            if (f.type === 'textarea') {
                formHtml += '<textarea name="'+f.name+'" '+(f.required?'required':'')+' style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:12px;min-height:70px;"></textarea>';
            } 
            else if (f.type === 'select') {
                formHtml += '<select name="'+f.name+'" '+(f.required?'required':'')+' style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:12px;">';
                formHtml += '<option value="">Pilih...</option>';
                f.options.forEach(function(o){ formHtml += '<option>'+o+'</option>'; });
                formHtml += '</select>';
            }
            else {
                formHtml += '<input type="'+(f.type==='number'||f.type==='email'||f.type==='tel'?f.type:'text')+'" name="'+f.name+'" '+(f.required?'required':'')+' style="width:100%;padding:8px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:12px;">';
            }
        });

        // button
        formHtml += '<button type="submit" style="width:100%;padding:10px;border:none;border-radius:'+getButtonRadius()+';background:'+getPrimaryColor()+';color:white;font-weight:600;cursor:pointer;">'+btnLabel+'</button>';
        formHtml += '<div id="'+formId+'_msg" style="margin-top:8px;font-size:12px;"></div>';

        formHtml += '</div>';

        // LEFT = image / RIGHT = form  
        html += columnLeft(imgBlock);
        html += columnRight('<form id="'+formId+'">'+formHtml+'</form>');

        html += layoutWrapper(false);
        return html;
    }

    document.write(buildFormHtml());

    window.addEventListener('load', function(){
        var form = document.getElementById(formId);
        var msg  = document.getElementById(formId+'_msg');
        if (!form) return;

        form.addEventListener('submit', function(e){
            e.preventDefault();
            msg.textContent = 'Mengirim...';
            msg.style.color = '#555';

            var fd = new FormData(form);
            var obj = {};
            fd.forEach((v,k)=> obj[k]=v);

            fetch(submitUrl, {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify({data:obj, source_url:window.location.href})
            })
            .then(r=>r.json())
            .then(j=>{
                if(j.success){
                    msg.style.color='#16a34a';
                    msg.textContent = j.message;
                    form.reset();
                } else {
                    msg.style.color='red';
                    msg.textContent = j.message || 'Gagal mengirim.';
                }
            })
            .catch(()=>{
                msg.style.color='red';
                msg.textContent='Gagal mengirim.';
            });
        });
    });

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

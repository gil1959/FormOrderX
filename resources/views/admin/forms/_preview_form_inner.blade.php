{{-- resources/views/admin/forms/_preview_form_inner.blade.php --}}

<div class="card-solid p-8">
  <h3 class="text-lg font-semibold text-slate-900 text-center mb-6">
    {{ $form->name }}
  </h3>

  <form onsubmit="return false;" class="space-y-4">
    @foreach ($fields as $field)
      <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-800">
          {{ $field->label }}
          @if ($field->required)
            <span class="text-rose-600">*</span>
          @endif
        </label>

        @if ($field->type === 'textarea')
          <textarea class="input" rows="3" disabled></textarea>

        @elseif($field->type === 'select')
          <select class="input" disabled>
            @foreach (($field->options ?? []) as $opt)
              <option>{{ $opt }}</option>
            @endforeach
          </select>

        @else
          <input
            type="{{ in_array($field->type, ['text','number','tel','email']) ? $field->type : 'text' }}"
            class="input"
            disabled
          >
        @endif
      </div>
    @endforeach

    @if ($varEnabled && count($varOptions))
      <div class="pt-4 border-t border-dashed border-slate-200 mt-4 space-y-2">
        <label class="text-sm font-semibold text-slate-800">{{ $varLabel }}</label>

        @if ($varType === 'dropdown')
          <select class="input" disabled>
            @foreach ($varOptions as $opt)
              <option>{{ $opt['label'] }}</option>
            @endforeach
          </select>
        @else
          <div class="flex flex-wrap gap-3">
            @foreach ($varOptions as $opt)
              <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="radio" disabled class="h-4 w-4 border-slate-300">
                <span>{{ $opt['label'] }}</span>
              </label>
            @endforeach
          </div>
        @endif
      </div>
    @endif

    <div class="mt-6 flex justify-center">
      <button type="button"
              class="inline-flex items-center px-6 py-2.5 text-white text-sm font-semibold shadow-lg {{ $btnColorClass }} {{ $btnShapeClass }}">
        {{ $btnLabel }}
      </button>
    </div>
  </form>
</div>

{{-- inner card only --}}

<h3 class="text-lg font-semibold text-gray-900 text-center mb-6">
    {{ $form->name }}
</h3>

<form onsubmit="return false;" class="space-y-4">

    @foreach ($fields as $field)
        <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @if ($field->type === 'textarea')
                <textarea class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                          rows="3" disabled></textarea>

            @elseif($field->type === 'select')
                <select class="block w-full rounded-md border-gray-300 shadow-sm text-sm" disabled>
                    @foreach ($field->options ?? [] as $opt)
                        <option>{{ $opt }}</option>
                    @endforeach
                </select>

            @else
                <input type="text"
                       class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                       disabled>
            @endif
        </div>
    @endforeach

    {{-- variation component --}}
    @if ($varEnabled && count($varOptions))
        <div class="space-y-1 pt-2 border-t border-dashed border-gray-200 mt-4">
            <label class="block text-sm font-medium text-gray-700">
                {{ $varLabel }}
            </label>

            @if ($varType === 'dropdown')
                <select class="block w-full rounded-md border-gray-300 shadow-sm text-sm" disabled>
                    @foreach ($varOptions as $opt)
                        <option>{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            @else
                <div class="flex flex-wrap gap-3">
                    @foreach ($varOptions as $opt)
                        <label class="inline-flex items-center gap-1 text-sm text-gray-700">
                            <input type="radio" disabled>
                            <span>{{ $opt['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <div class="mt-6 flex justify-center">
        <button type="button"
                class="inline-flex items-center px-6 py-2.5 text-white text-sm font-semibold shadow-lg
                {{ $btnColorClass }} {{ $btnShapeClass }}">
            {{ $btnLabel }}
        </button>
    </div>

</form>

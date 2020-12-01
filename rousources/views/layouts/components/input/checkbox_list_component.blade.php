@inject('component','LaravelSupports\Views\Components\Inputs\CheckBoxListComponent')

@foreach($items as $values)
    <div class="{{ $divClass }}">
        <input type="checkbox" id="{{ $values[$component::KEY_KEY] }}" name="{{ $values[$component::KEY_NAME] }}"
               class="{{ $inputClass }}"
               value="{{ $values[$component::KEY_VALUE] }}"
               @if($values[$component::KEY_CHECKED])
               checked
            @endif
        >
        <label class="{{ $labelClass }}"
               for="{{ $values[$component::KEY_KEY] }}">{{ $values[$component::KEY_TEXT] }}</label>
    </div>
    @if($values[$component::KEY_IS_OTHER] == true)
        @php($otherID = $values[$component::KEY_KEY])
        @php($otherInputID = $otherID.'_input')
        <div class="input-group mb-3">
            <input type="text" class="{{ $othersClass }}" id="{{ $otherInputID }}" name="{{ $otherInputID }}"
                   placeholder="{{ $values[$component::KEY_OTHER_HINT] }}"
                   @if($values[$component::KEY_CHECKED])
                   value="{{ $values[$component::KEY_OTHER_VALUE] }}"
                   @endif
                   aria-label="{{ $values[$component::KEY_OTHER_HINT] }}"
                   @if(!$values[$component::KEY_CHECKED])
                   readonly
                @endif
            >
        </div>

        <script>
            $(function () {
                $('#{{ $otherID }}').on('click', function () {
                    $('#{{ $otherInputID }}').attr('readonly', !this.checked);
                });
            });
        </script>
    @endif
@endforeach

@isset($footer)
    {{ $footer }}
@endif

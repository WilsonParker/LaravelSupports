@inject('component','LaravelSupports\Views\Components\Inputs\CheckBoxListComponent')

@foreach($items as $values)
    <div class="{{ $divClass }}">
        <input type="checkbox" id="{{ $values[$component::KEY_KEY] }}" name="{{ $values[$component::KEY_NAME] }}"
               class="{{ $inputClass }}"
               value="{{ $values[$component::KEY_VALUE] }}"
               @if($values[$component::KEY_CHECKED])
               checked
            @endif>
        <label class="{{ $labelClass }}"
               for="{{ $values[$component::KEY_KEY] }}">{{ $values[$component::KEY_TEXT] }}</label>
    </div>
    {!! $buildOthersInput($values) !!}
    {!! $buildOthersScript($values) !!}
@endforeach
{!! $buildMaxSelectableScript() !!}
@isset($footer)
    {{ $footer }}
@endif

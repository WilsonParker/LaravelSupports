@inject('component','LaravelSupports\Views\Components\Inputs\GridCheckBoxListComponent')

<div class="container">
    @php($length =  sizeof($items))
    @foreach($items as $index => $values)
        @if($index % $rows == 0)
            <div class="{{ $rowClass }}">
                @endif
                <div class="{{ $colClass }}">
                    <input type="checkbox" id="{{ $values[$component::KEY_KEY] }}"
                           name="{{ $values[$component::KEY_NAME] }}"
                           class="{{ $inputClass }}"
                           value="{{ $values[$component::KEY_VALUE] }}"
                           @if($values[$component::KEY_CHECKED])
                           checked
                        @endif
                    >
                    <label class="{{ $labelClass }}"
                           for="{{ $values[$component::KEY_KEY] }}">{{ $values[$component::KEY_TEXT] }}</label>
                </div>
                @if($index % $rows == $rows - 1 || $index == $length - 1)
            </div>
        @endif
    @endforeach
</div>
{!! $buildMaxSelectableScript() !!}
@isset($footer)
    {{ $footer }}
@endif

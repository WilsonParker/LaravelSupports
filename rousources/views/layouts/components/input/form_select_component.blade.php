@inject('component','LaravelSupports\Views\Components\Inputs\FormSelectComponent')

<div class="{{ $divClass }}" {{ $divAttr }}>
    <label class="{{ $labelClass }}" for="{{ $id }}">{{ $label }}</label>
    <select class="{{ $selectClass }}"
            @if($id != '')
            id="{{ $id }}"
            @endif
            name="{{ $name }}" {{ $selectAttr }}>
        @foreach($items as $item)
            <option value="{{ $item[$component::KEY_VALUE] }}"
                    @if((old($name, '') == $item[$component::KEY_VALUE] || isset($selectedValue) && $selectedValue == $item[$component::KEY_VALUE]))
                    selected
                @endif
                {{ $item[$component::KEY_OPTION_ATTR] }}
            >{{ $item[$component::KEY_TEXT] }}</option>
        @endforeach
    </select>
</div>

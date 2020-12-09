@inject('component','LaravelSupports\Views\Components\Inputs\FormSelectComponent')

<div class="{{ $divClass }}" {{ $divAttr }}>
    <label class="{{ $labelClass }}" for="{{ $id }}">{{ $label }}</label>
    <select class="{{ $selectClass }}"
            @if($id != '')
            id="{{ $id }}"
            @endif
            name="{{ $name }}" {{ $selectAttr }}>
        @foreach($items as $item)
            @foreach($item as $text => $value)
                <option value="{{ $value }}"
                        @if(isset($selectedValue) && $selectedValue == $value)
                        selected
                    @endif
                >{{ $text }}</option>
            @endforeach
        @endforeach
    </select>
</div>

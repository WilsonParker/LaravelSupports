@inject('component','LaravelSupports\Views\Components\Inputs\FormInputComponent')

<div class="{{ $divClass }}" {{ $divAttr }}>
    <label class="{{ $labelClass }}" for="{{ $id }}">{{ $label }}</label>
    <input type="text" class="{{ $inputClass }}"
           @if($id != '')
           id="{{ $id }}"
           @endif
           name="{{ $name }}" value="{{ old($name, $value ?? '') }}" aria-describedby="{{ $id }}_help" {{ $inputAttr }}/>
    @if($hasHelp)
        <small
            @if($id != '')
            id="{{ $id }}_help"
            @endif
            class="{{ $helpClass }}">{{ $help }}</small>
    @endif
</div>

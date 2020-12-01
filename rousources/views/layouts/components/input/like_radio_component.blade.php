@inject('component','LaravelSupports\Views\Components\Inputs\LikeRadioComponent')
<div class="form-group">
    @isset($title)
        {{ $title }}
    @endif

    @foreach($items as $values)
        <div class="{{ $divClass }}">
            <input class="{{ $inputClass }}" type="radio" id="input_radio_{{ $name }}_{{ $values[$component::KEY_VALUE] }}"
                   name="{{ $name }}"
                   value="{{ $values[$component::KEY_VALUE] }}"
                   @if($values[$component::KEY_IS_CHECKED])
                       checked
                   @endif
                   style="display: none">
            <label class="{{ $labelClass }}" for="input_radio_{{ $name }}_{{ $values[$component::KEY_VALUE] }}">
                <i class="" aria-hidden="true"
                   style="margin-right: 5px"
                   data-checked="fa {{ $values[$component::KEY_CHECKED] }}"
                   data-non-checked="fa {{ $values[$component::KEY_NON_CHECKED] }}"
                ></i>
                {{ $values[$component::KEY_TEXT] }}
            </label>
        </div>
    @endforeach
</div>

<script>
    $(function () {
        setIcons();

        $('.input_radio').on('click', function () {
            setIcons();
        });
    });

    function setIcons() {
        $('.input_radio').each(function (index, item) {
            let i = $($(item).parent()[0].querySelector('i'));
            let cls = this.checked ? i.attr('data-checked') : i.attr('data-non-checked');
            i.attr('class', 'fa ' + cls);
        });
    }
</script>

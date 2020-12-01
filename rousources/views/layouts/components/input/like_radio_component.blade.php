@inject('component','LaravelSupports\Views\Components\Inputs\LikeRadioComponent')
<div class="form-group">
    @isset($title)
        {{ $title }}
    @endif

    @foreach($items as $values)
        @php($checked = $values[$component::KEY_IS_CHECKED])
        <div class="{{ $divClass }}">
            <input class="{{ $inputClass }}" type="radio"
                   id="input_radio_{{ $name }}_{{ $values[$component::KEY_VALUE] }}"
                   name="{{ $name }}"
                   value="{{ $values[$component::KEY_VALUE] }}"
                   @if($checked)
                   checked
                   @endif
                   style="display: none">
            <label class=""
                   data-checked="{{ $values[$component::KEY_CHECKED_LABEL] }}"
                   data-non-checked="{{ $values[$component::KEY_NON_CHECKED_LABEL] }}"
                   for="input_radio_{{ $name }}_{{ $values[$component::KEY_VALUE] }}">
                <i class="" aria-hidden="true"
                   style="margin-right: 5px"
                   data-checked="fa {{ $values[$component::KEY_CHECKED_ICON] }}"
                   data-non-checked="fa {{ $values[$component::KEY_NON_CHECKED_ICON] }}"
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
            let label = $($(item).parent()[0].querySelector('label'));
            toggleClass('fa', this.checked, i)
            toggleClass('{{ $labelClass }}', this.checked, label)
        });
    }

    function toggleClass(prefix, checked, tag) {
        let checkedClass = prefix + ' ' + tag.attr('data-checked');
        let nonCheckedClass = prefix + ' ' + tag.attr('data-non-checked');
        if (checked) {
            tag.attr('class', checkedClass);
        } else {
            tag.attr('class', nonCheckedClass);
        }
    }
</script>

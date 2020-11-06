<div class="form-group">
    @isset($title)
        {{ $title }}
    @endif

    @foreach($items as $values)
        <div class="form-check form-check-inline">
            <input class="form-check-input input_radio" type="radio" id="input_radio_{{ $name }}_{{ $values['value'] }}"
                   name="{{ $name }}" style="display: none">
            <i class="" aria-hidden="true"
               style="margin-right: 5px"
               data-checked="fa {{ $values['checked'] }}"
               data-non-checked="fa {{ $values['non_checked'] }}"
            ></i>
            <label class="form-check-label" for="input_radio_{{ $name }}_{{ $values['value'] }}">
                {{ $values['text'] }}
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

@inject('component','LaravelSupports\Views\Components\Inputs\UploadImageComponent')

<div {{ $rootDivAttr }}>
<div class="input-group mb-3">
    <div class="input-group-prepend">
        <span class="input-group-text" id="{{ $id }}Addon">{{ $text }}</span>
    </div>
    <div class="custom-file" {{ $divAttr }}>
        <input type="file" class="custom-file-input"
               @if($id != '')
               id="{{ $id }}"
               @endif
               name="{{ $name }}"
               aria-describedby="{{ $id }}Addon" accept=".jpg,.gif,.png"
            {{ $inputAttr }}
        >
        <label class="custom-file-label" for="{{ $id }}">Choose file</label>
    </div>
</div>
@if($needPreview)
    <img class="{{ $imgClass }}" id="img_{{ $id }}" src="{{ $src }}" alt=""/>
@endif
</div>

<script>
    $(function () {
        $('.custom-file-input').on('change', function () {
            //get the file name
            let fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fileName);

            @if($needPreview)
            let file = this.files[0];
            let img = $("#img_" + this.id);
            let reader = new FileReader();
            // Set preview image into the popover data-content
            reader.onload = function (e) {
                img.attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
            @endif
        })
    })
</script>

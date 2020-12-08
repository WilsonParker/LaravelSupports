@inject('component','LaravelSupports\Views\Components\Inputs\UploadImageComponent')

<div class="input-group mb-3">
    <div class="input-group-prepend">
        <span class="input-group-text" id="{{ $id }}Addon">{{ $text }}</span>
    </div>
    <div class="custom-file">
        <input type="file" class="custom-file-input" id="{{ $id }}"
               data-id="{{ $id }}" name="{{ $id }}"
               aria-describedby="{{ $id }}Addon" accept=".jpg,.gif,.png">
        <label class="custom-file-label" for="{{ $id }}">Choose file</label>
    </div>
</div>
@if($needPreview)
    <img class="{{ $imgClass }}" data-id="{{ $id }}" src="{{ $src }}" alt=""/>
@endif

<script>
    $(function () {
        $('.custom-file-input').on('change', function () {
            //get the file name
            let fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fileName);

            @if($needPreview)
            let id = $(this).attr('data-id');
            let file = this.files[0];
            let img = modelBuilder.selector.getModel('img', id);
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

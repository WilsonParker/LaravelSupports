<!-- Modal -->
@isset($modalRoot)
    {!! $modalRoot !!}
@else
    <div class="modal fade"
         id="basicModal"
         data-backdrop=" static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true"
         style="{{ $modalRootStyle ?? ""}}">
@endif
    @isset($modalDialog)
        {!! $modalDialog !!}
    @else
        <div class="modal-dialog modal-xl" style="{{ isset($modalDialogStyle) ?? $modalDialogStyle}}">
            @endif
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">FlyBook Modal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @yield('modal_body')
                </div>
                <div class="modal-footer">
                    <!-- Copyright -->
                    <div class="footer-copyright text-center text-black-50 py-3">© developed by:
                        <a class="dark-grey-text" href="https://mdbootstrap.com/"> WilsonParker@flybook.kr</a>
                    </div>
                    @hasSection('modal_bottom')
                        @yield('modal_bottom')
                    @else
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @endif
                </div>
            </div>
        </div>
</div>

<script>
    $(function () {
        $('#basicModal').on('show.bs.modal', function (e) {
            let loadURL = $(e.relatedTarget).data('load-url');
            $(this).find('#basicModal .modal-body').load(loadURL);
        });
    })
</script>
@yield('modal_script')

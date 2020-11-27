@push('styles')
    <link href="{{ asset('css/datatable-horizontal-scroll.css') }}" rel="stylesheet" type="text/css">
@endpush

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
    </div>
    <div class="card-body">
        @isset($headAdded)
            <div class="col-sm-12 col-md-9 row">
                <div class="input-group mb-3 dataTables_length">
                    {{ $headAdded }}
                </div>
            </div>
        @endisset

        <div class="table-responsive">
            <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    {!! $header !!}
                </tr>
                </thead>
                <tfoot>
                <tr>
                    {!! $header !!}
                </tr>
                </tfoot>
                <tbody>
                {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('body_scripts')
    <!-- Page level custom scripts -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "scrollX": true,
                "order": [[ "{{ $sort[0] }}", "{{ $sort[1] }}" ]]
            });
            $('.dataTables_length').addClass('bs-select');
        });

        function downloadExcel() {

        }
    </script>
@endpush

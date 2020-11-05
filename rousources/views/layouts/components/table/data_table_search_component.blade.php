@inject('baseViewModel', 'LaravelSupports\ViewModels\BaseViewModel)
@inject('tableSearchComponent', 'LaravelSupports\Views\Components\Tables\DataTableSearchComponent)

@push('styles')
    <style>
        #dataTable th, td {
            white-space: nowrap;
        }

        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc_disabled:after,
        table.dataTable thead .sorting_asc_disabled:before,
        table.dataTable thead .sorting_desc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc_disabled:after,
        table.dataTable thead .sorting_desc_disabled:before {
            bottom: .5em;
        }

    </style>
@endpush

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <form name="frm" action="{{ $url ?? '' }}">
                    <input type="hidden" name="page" value="1"/>
                    <div class="col-sm-12 col-md-9 row">
                        @isset($headAdded)
                            <div class="input-group mb-3 dataTables_length">
                                {{ $headAdded }}
                            </div>
                        @endisset
                    </div>
                    @isset($footerAdded)
                        <div class="input-group mb-3 dataTables_length">
                            {{ $footerAdded }}
                        </div>
                    @endisset
                    <div class="col-sm-12 col-md-9 row">
                        <div class="input-group mb-3 dataTables_length">
                            @isset($tableSearch)
                                {{ $tableSearch }}
                            @endisset
                            @isset($tableBasicSearch)
                                {{ $tableBasicSearch }}
                            @else
                                @foreach($search as $key => $values)
                                    <label>{{ $values[$baseViewModel::KEY_SEARCH_LABEL] }}
                                        <select class="custom-select custom-select-sm form-control form-control-sm"
                                                name="{{$key}}">
                                            @foreach($values[$baseViewModel::KEY_SEARCH_VALUES] as $itemKey => $itemValue)
                                                <option value="{{ $itemKey }}"
                                                        @if(isset($searchData[$key]) && $searchData[$key] == $itemKey)
                                                        selected
                                                    @endif
                                                >{{ $itemValue }}</option>
                                            @endforeach
                                        </select>
                                    </label>&nbsp;
                                @endforeach
                            @endisset
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 row">
                        <div class="input-group mb-3 dataTables_length">
                            <input type="text" class="form-control" name="{{ $dataTableSearchComponent::KEY_KEYWORD }}"
                                   value="{{ $searchData[$dataTableSearchComponent::KEY_KEYWORD] ?? '' }}"
                                   aria-label="Search Text">

                            <button type="button" class="btn btn-primary" onclick="tableSearch()"><i
                                    class="fas fa-search fa-sm"></i></button>
                            <button type="button" class="btn btn-success" onclick="clearAndReloadPage()">clear
                            </button>
                        </div>
                    </div>
                </form>
            </div>

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
                    "scrollX": true
                });
                $('.dataTables_length').addClass('bs-select');
            });

            function tableSearch() {
                let callback = function () {
                    frm.submit();
                };
                loading.run(callback);
            }

            function clearAndReloadPage() {
                let callback = function () {
                    location.href = "?";
                };
                loading.run(callback);
            }

            function getTableObject() {
                return $("#table_search_component");
            }
        </script>
@endpush

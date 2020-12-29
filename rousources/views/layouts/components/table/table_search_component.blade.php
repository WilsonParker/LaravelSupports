@inject('baseViewModel', 'LaravelSupports\ViewModels\BaseViewModel)
@inject('tableSearchComponent', 'LaravelSupports\Views\Components\Tables\TableSearchComponent)

<!-- DataTales -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <form name="frm" action="{{ $url }}">
                    <input type="hidden" name="page" value="1"/>
                    <div class="col-sm-12 col-md-9 row">
                        @isset($headAdded)
                            <div class="input-group mb-3 dataTables_length">
                                {{ $headAdded }}
                            </div>
                        @endisset
                        <div class="input-group mb-3 dataTables_length">
                            @isset($tableLength)
                                {{ $tableLength }}
                            @else
                                <label>Show
                                    <select name="{{ $tableSearchComponent::KEY_PAGINATE_LENGTH }}"
                                            aria-controls="dataTable"
                                            class="custom-select custom-select-sm form-control form-control-sm"
                                            onchange="onLengthChanged(this.value)"
                                    >
                                        @foreach($length as $value)
                                            <option value="{{ $value }}"
                                                    @if(isset($searchData[$tableSearchComponent::KEY_PAGINATE_LENGTH]) && $searchData[$tableSearchComponent::KEY_PAGINATE_LENGTH] == $value)
                                                    selected
                                                @endif
                                            >{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            @endisset
                            @isset($tableTotal)
                                {{ $tableTotal }}
                            @else
                                <span style="margin-left: 15px">
                        Total : {{ number_format($link->total()) }}
                    </span>
                            @endisset
                        </div>
                    </div>
                    @if(isset($sort) && !empty($sort))
                        <div class="col-sm-12 col-md-9 row">
                            <div class="input-group mb-3 dataTables_length">
                                @foreach($sort as $key => $values)
                                    <label>정렬
                                        <select class="custom-select custom-select-sm form-control form-control-sm"
                                                name="{{$key}}"
                                        >
                                            @foreach($values[$baseViewModel::KEY_SORT_VALUES] as $itemKey => $itemValue)
                                                <option value="{{ $itemKey }}"
                                                        @if(isset($searchData[$key]) && $searchData[$key] == $itemKey)
                                                        selected
                                                    @endif
                                                >{{ $itemValue }}</option>
                                            @endforeach
                                        </select>
                                    </label>&nbsp;
                                @endforeach
                            </div>
                        </div>
                    @endisset
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
                                                @if($values[$baseViewModel::KEY_SEARCH_TYPE] == $baseViewModel::KEY_SEARCH_TYPE_MULTIPLE))
                                                    name="{{$key}}[]"
                                                @else
                                                    name="{{$key}}"
                                                @endif
                                                @if($values[$baseViewModel::KEY_SEARCH_TYPE] == $baseViewModel::KEY_SEARCH_TYPE_MULTIPLE)
                                                    multiple
                                                @endif
                                        >
                                            @foreach($values[$baseViewModel::KEY_SEARCH_VALUES] as $itemKey => $itemValue)
                                                <option value="{{ $itemKey }}"
                                                        @if(isset($searchData[$key]))
                                                            @if($values[$baseViewModel::KEY_SEARCH_TYPE] == $baseViewModel::KEY_SEARCH_TYPE_MULTIPLE && in_array($itemKey, $searchData[$key]))
                                                            selected
                                                            @elseif($searchData[$key] == $itemKey)
                                                            selected
                                                            @endif
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
                            <input type="text" class="form-control" name="{{ $tableSearchComponent::KEY_KEYWORD }}"
                                   value="{{ $searchData[$tableSearchComponent::KEY_KEYWORD] ?? '' }}"
                                   aria-label="Text input with segmented dropdown button">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="tableSearch()"><i
                                        class="fas fa-search fa-sm"></i></button>
                                <button type="button" class="btn btn-success" onclick="clearAndReloadPage()">clear
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                @empty($tableRoot)
                    <table class="table table-bordered" width="100%" cellspacing="0" id="{{ $id }}">
                        @else
                            {!! $tableRoot !!}
                        @endif
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
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                        {{ $link->appends($searchData)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('body_scripts')
    <script>
        function onLengthChanged(length) {
            let callback = function () {
                location.href = "?" + helper.html.addQueryString("length", length);
            };
            loading.run(callback);
        }

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
            return $("#{{ $id }}");
        }
    </script>
@endpush

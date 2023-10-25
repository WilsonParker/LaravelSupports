<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('admin.layouts.includes.meta')

<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('admin.layouts.includes.scripts')

    @include('admin.layouts.includes.styles')
</head>
{{--<body oncontextmenu="return false;">--}}
<body id="page-top">
<div id="app">
    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('admin.layouts.includes.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                @include('admin.layouts.includes.topbar')
                @yield('content')
                @include('admin.layouts.includes.footer')
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

{{--@include('admin.layouts.includes.logout_modal')--}}

@hasSection('modal')
    @yield('modal')
@else
    @include('admin.layouts.components.modals.modal_template')
@endif

@unless(empty($components))
    @foreach($components as $component)
        @include('admin.layouts.components.'.$component)
    @endforeach
@endunless

@include('admin.layouts.includes.body_scripts')

@include('admin.layouts.includes.loading')
</body>
</html>

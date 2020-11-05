<!-- Bootstrap core JavaScript-->
<script src="{{ asset('resources/template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('resources/template/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('resources/template/js/sb-admin-2.js')}}"></script>

<!-- Page level plugins -->
<script src="{{ asset('resources/template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('resources/template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- date picker -->
<script src="{{ asset('resources/datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

<script>
    function locateUrl(url) {
        let callback = function () {
            location.href = url;
        };
        loading.run(callback);
    }

    /*
    * 에러 발생 시 alert 실행
    * */
    $(function () {
        @if($errors->any())
        alert("{{ implode('', $errors->all(':message')) }}");
        @endif

        @if (session()->has('message'))
        alert("{{ session()->get('message') }}");
        @endif
    });
</script>
@stack('body_scripts')

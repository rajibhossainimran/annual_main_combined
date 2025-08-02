<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Tender Management System</title>

    <!-- Fonts -->
    <link href="{{ asset('admin/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}?v=1.0.1" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/toastr.min.css') }}">
    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body>
    {{-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div> --}}
    {{ $slot }}
    @if (Session::has('message'))
    <div id="session-success" class="d-none">{{ session('message') }}</div>
    @endif

    @if (Session::has('error'))
    <div id="session-error" class="d-none">{{ session('error') }}</div>
    @endif

    @if (Session::has('info'))
    <div id="session-info" class="d-none">{{ session('info') }}</div>
    @endif

    @if (Session::has('warning'))
    <div id="session-warning" class="d-none">{{ session('warning') }}</div>
    @endif
    </div>
    @if ($errors->any())
    <div class="position-absolute footer-right">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <span>
                    <p>{{ $error }}</p>
                </span>
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif
    <script type="text/javascript" src="{{ asset('admin/scripts/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/scripts/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/scripts/main.js') }}"></script>
</body>

</html>

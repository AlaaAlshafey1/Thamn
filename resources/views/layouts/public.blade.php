<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .main-logo {
            max-height: 50px;
        }

        .page-content {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .section-title {
            color: #c1953e;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        footer {
            padding: 20px 0;
            text-align: center;
            color: #666;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container d-flex justify-content-center">
            <a class="navbar-brand" href="/">
                <img src="{{ URL::asset('assets/img/Logo.png') }}" alt="Logo" class="main-logo">
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="page-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
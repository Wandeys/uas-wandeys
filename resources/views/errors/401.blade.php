<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>401 Unauthorized | {{ $setting?->app_name ?? 'SIMANA' }}</title>

    <!-- Favicons -->
    <link href="{{ ($setting && $setting->logo) ? asset('storage/' . $setting->logo) : asset('niceadmin/img/laravel.png') }}" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('niceadmin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('niceadmin/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('niceadmin/css/style.css') }}" rel="stylesheet">
</head>

<body>

    <main>
        <div class="container">

            <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
                <h1>401</h1>
                <h2>Akses Tidak Diizinkan (Unauthorized).</h2>
                <p class="text-muted mb-4">Sesi Anda tidak valid atau Anda tidak memiliki otorisasi yang sah untuk mengakses rute ini.</p>
                <a class="btn btn-primary d-inline-flex align-items-center px-4 py-2" href="{{ route('dashboard.index') }}">
                    <i class="bi bi-arrow-left-short fs-4 me-1"></i> Kembali ke Dashboard
                </a>
                <img src="{{ asset('niceadmin/img/not-found.svg') }}" class="img-fluid py-5" alt="Page Not Found" style="max-height: 300px;">
            </section>

        </div>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('niceadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>

</html>

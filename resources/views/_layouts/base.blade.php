<!DOCTYPE html>
<html lang="en" class="h-100" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @vite(['resources/scss/bootstrap.scss', 'resources/scss/site.scss', 'resources/js/app.js', 'resources/css/datatables.css'])
    @livewireStyles
    <title>@if(isset($_pageTitle)) {{ $_pageTitle }} :: @endif natTrak :: VATSIM</title>
</head>
@yield('layout')
@livewireScripts
@if (Session::has('alert'))
    <script>
        window.onload = (event) => {
            Swal.fire(
                    <?php echo(json_encode(Session::get('alert'))) ?>
            )
        };
    </script>
@endif
</html>

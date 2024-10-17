@extends('_layouts.main')
@section('page')
    <div class="container">
        <h1 class="fs-2 font-display text-primary-emphasis">About natTrak</h1>
        <div class="vstack gap-3 mt-4">
            <p>
                A VATSIM service for oceanic clearances in the Atlantic oceanic regions.
                <br/>
                <br/>
                Built using Laravel 10, Livewire, and Bootstrap 5.
            </p>
            <h5 class="font-display text-primary-emphasis">
                Suggestions? Bug reports?
            </h5>
            <p>
                Contact us via email (<a href="#" id="a" onclick="showEmail()">show email</a>) or make an issue on <a
                    href="https://github.com/vatsimnetwork/nattrak">GitHub.</a>
            </p>
        </div>
    </div>
    <script>
        function showEmail() {
            $("#a").html(window.atob("bGllc2VsLmRvd25lc0B2YXRzaW0ubmV0"));
        }
    </script>
@endsection

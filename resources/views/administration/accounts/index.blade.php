@extends('_layouts.main')
@section('page')
    <div class="container">
        <h2 class="fs-2 font-display text-primary-emphasis">Manage admin permissions</h2>
        <p class="text-secondary">
            <span class="fw-bold">Showing users with privileged access.</span> Showing users with controller access. Temporary controller access is also assigned when a user is logged onto an oceanic position. To add a user, use the form at the bottom of the page.
        </p>
        <livewire:administration.administrators-table/>
        <div class="mt-4">
            <h5>Add privileged user</h5>
            <form action="{{ route('administration.accounts.add-access') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="id" placeholder="CID" class="form-control">
                    <button class="btn btn-outline-success" type="submit">Add</button>
                </div>
            </form>
        </div>
    </div>
    <script type="module">
        $(document).ready(function () {
            let table = new DataTable('#dataTable', {
                responsive: true
            });
        })
    </script>
@endsection

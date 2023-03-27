@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-right uk-padding-remove-left">
        <h2>Manage controller permissions</h2>
        <p class="uk-text-meta">
            <span class="uk-text-bold">Showing users with privileged access.</span> Showing users with controller access. Temporary controller access is also assigned when a user is logged onto an oceanic position. To add a user, use the form at the bottom of the page.
        </p>
        <table id="dataTable" class="dataTable uk-table uk-table-hover uk-table-striped uk-table-condensed">
                <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Permission Level</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($privilegedAccounts as $account)
                        <tr>
                            <td>{{ $account->id }}</td>
                            <td>{{ $account->given_name }} {{ $account->surname }}</td>
                            <td>{{ $account->access_level->name }}</td>
                            <td>
                                <form method="POST" action="{{ route('administration.accounts.remove-access') }}">
                                    @csrf
                                    <input type="hidden" name="vatsimAccountId" value="{{ $account->id }}">
                                    <button class="uk-button uk-button-small" name="delete" type="submit">Remove privileged access</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <h5>Add privileged user</h5>
            <form action="{{ route('administration.accounts.add-access') }}" method="POST" class="uk-grid-small" uk-grid>
                @csrf
                <div class="uk-grid-1-2@s">
                    <input class="uk-input" type="text" name="id" placeholder="Controller CID">
                </div>
                <div class="uk-grid-1-4@s">
                    <button class="uk-button" type="submit">Add</button>
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

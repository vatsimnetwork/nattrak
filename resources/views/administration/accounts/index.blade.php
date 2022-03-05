@extends('_layouts.main')
@section('page')
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Manage Users
            </p><hr />
            <p class="small">Showing users with privileged access. To add a user, use the form at the bottom of the page.</p>
            <table class="table table-borderless table-striped">
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
                                    <button class="btn btn-danger btn-sm" name="delete" type="submit">Remove privileged access</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <h5>Add privileged user</h5>
            <form action="{{ route('administration.accounts.add-access') }}" method="POST">
                @csrf
                <input class="form-control" type="text" name="id" placeholder="Controller CID">
                <button class="btn btn-primary" type="submit">Add Controller</button>
            </form>
        </div>
    </div>
@endsection

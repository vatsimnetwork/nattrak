@extends('_layouts.main')
@section('page')
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Manage Controller Permissions
            </p><hr />
            <p class="small"><b>Do not add people who already have admin or above perms.</b> Showing users with controller access. Temporary controller access is also assigned when a user is logged onto an oceanic position. To add a user, use the form at the bottom of the page.</p>
            <table id="dataTable" class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Permission Level</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($controllerAccounts as $account)
                        <tr>
                            <td>{{ $account->id }}</td>
                            <td>{{ $account->given_name }} {{ $account->surname }}</td>
                            <td>{{ $account->access_level->name }}</td>
                            <td>
                                <form method="POST" action="{{ route('administration.controllers.remove-access') }}">
                                    @csrf
                                    <input type="hidden" name="vatsimAccountId" value="{{ $account->id }}">
                                    <button class="btn btn-danger btn-sm" name="delete" type="submit">Remove controller access</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <h5>Add controller</h5>
            <form action="{{ route('administration.controllers.add-access') }}" method="POST">
                @csrf
                <input class="form-control" type="text" name="id" placeholder="Controller CID">
                <button class="btn btn-primary" type="submit">Add</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready( function () {
            $('#dataTable').DataTable();
        } );
    </script>
@endsection

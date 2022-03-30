@extends('_layouts.main')
@section('page')
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <div class="row inside shadow pb-5">
        <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            <p class="header">
                Activity log
            </p><hr />
            <table id="dataTable" class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th scope="col">Time</th>
                    <th scope="col">Description</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Causer</th>
                    <th scope="col">Properties</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($log as $entry)
                        <tr>
                            <td>{{ $entry->created_at }}</td>
                            <td>{{ $entry->description }}</td>
                            <td>{{ $entry->subject_type }} {{ $entry->subject_id }}</td>
                            <td>{{ $entry->causer_type }} {{ $entry->causer_id }}</td>
                            <td>
                                <code>
                                    {{ $entry->properties }}
                                </code>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready( function () {
            $('#dataTable').DataTable();
        } );
    </script>
@endsection

@extends('_layouts.main')
@section('page')
    <div class="uk-container uk-padding uk-padding-remove-right uk-padding-remove-left">
        <h2>
            Activity log
        </h2>
        <hr>
        <table id="dataTable" class="dataTable uk-table uk-table-hover uk-table-striped">
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
    <script type="module">
        $(document).ready( function () {
            let table = new DataTable('#dataTable', {
                responsive: true
            });
        } );
    </script>
@endsection

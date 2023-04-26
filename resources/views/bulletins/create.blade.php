@extends('_layouts.main')
@section('page')
    <div class="container">
        <div class="mb-4 d-flex flex-row justify-content-between align-items-center">
            <h2 class="fs-2 font-display text-primary-emphasis">Create NOTAM</h2>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p class="fw-bold">Some input was incorrect.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('notams.store') }}">
            @csrf
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="title" id="title" placeholder="Title" required>
                <label for="title">NOTAM title</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="subtitle" id="subtitle" placeholder="Subtitle (optional)">
                <label for="subtitle">Subtitle (optional)</label>
            </div>
            <div class="form-floating mb-3">
                <textarea class="form-control" rows="5" name="content" id="content" placeholder="Content" required></textarea>
                <label for="content">Content (plain text)</label>
            </div>
            <div class="form-floating mb-3">
                <input type="url" class="form-control" name="action_url" id="action_url" placeholder="Action button URL (optional)">
                <label for="action_url">Action button URL (optional)</label>
            </div>
            <div>
                <button class="btn btn-success">Create</button>
            </div>
        </form>
    </div>
@endsection

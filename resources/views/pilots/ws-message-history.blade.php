@extends('_layouts.main')
@section('page')
    <div class="container">
        <h1 class="fs-2 mb-4 font-display text-primary-emphasis">Message history</h1>
        <livewire:pilots.message-history/>
    </div>
    <script type="module">
        let id = {{ auth()->id() }}
        Echo.private(`clearance.${id}`)
            .listen('.clx.issued', (e) => {
                console.log(e)
            })
    </script>
@endsection

@extends('_layouts.main')
@section('page')
    <livewire:controllers.create-manual-clx :dl-authorities="$dlAuthorities" :active-dl-authority="$activeDlAuthority" :tracks="$tracks" />
@endsection

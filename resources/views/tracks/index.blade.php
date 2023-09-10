@extends('_layouts.main')
@section('page')
    <div class="container">
        <h2 class="fs-2 font-display text-primary-emphasis mb-4">Tracks</h2>
        <div class="alert alert-info">
            <span>This page shows the tracks in use on natTrak. These tracks are sourced from the VATSIM Gander Oceanic tracks API. Active tracks are in use and valid for requesting clearances. Inactive tracks are no longer valid and cannot be requested, but remain visible for clearances that were on that track. Concorde tracks are based off historical data and are request-able by aircraft detected as Concorde only.</span>
        </div>
        <div class="vstack gap-4">
            <div>
                <h5 class="mb-3 font-display">Active tracks</h5>
                <ul class="list-group">
                    @foreach($activeTracks as $track)
                        <li class="list-group-item">
                            <div class="d-flex flex-row gap-4 align-content-start">
                                <div class="fs-2">
                                    {{ $track->identifier }}
                                </div>
                                <div class="">
                                    <p class="font-monospace mb-1">
                                        {{ $track->last_routeing }}
                                    </p>
                                    <span>
                                        Active from {{ $track->valid_from }} to {{ $track->valid_to }}
                                    </span>
                                    <br>
                                    <span>
                                        @if($track->predominantly_odd_or_even)Primarily {{ $track->predominantly_odd_or_even }} levels.@endif Valid at @foreach ($track->flight_levels as $fl)FL{{ altitudeToFlightLevel($fl) }}@if(!$loop->last), @endif @endforeach
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    @if($activeTracks->isEmpty())
                        <li class="list-group-item">
                            No tracks active.
                        </li>
                    @endif
                </ul>
            </div>
            <div>
                <h5 class="mb-3 font-display">Inactive tracks</h5>
                <ul class="list-group">
                    @foreach($inactiveTracks as $track)
                        <li class="list-group-item">
                            <div class="d-flex flex-row gap-4 align-content-start">
                                <div class="fs-2">
                                    {{ $track->identifier }}
                                </div>
                                <div class="">
                                    <p class="font-monospace mb-1">
                                        {{ $track->last_routeing }}
                                    </p>
                                    <span>
                                        Last active {{ $track->last_active }}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    @if($inactiveTracks->isEmpty())
                        <li class="list-group-item">
                            No tracks inactive.
                        </li>
                    @endif
                </ul>
            </div>
            <div>
                <h5 class="mb-3 font-display">Concorde tracks</h5>
                <ul class="list-group">
                    @foreach($concordeTracks as $track)
                        <li class="list-group-item">
                            <div class="d-flex flex-row gap-4 align-content-start">
                                <div class="fs-2">
                                    {{ $track->identifier }}
                                </div>
                                <div class="">
                                    <p class="font-monospace mb-1">
                                        {{ $track->last_routeing }}
                                    </p>
                                    <span>
                                        @switch($track->identifier)
                                            @case('SM')
                                                Westbound track.
                                                @break
                                            @case('SN')
                                                Eastbound track.
                                                @break
                                            @case('SO')
                                                Bidirectional track.
                                                @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    @if($concordeTracks->isEmpty())
                        <li class="list-group-item">
                            No Concorde tracks.
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection

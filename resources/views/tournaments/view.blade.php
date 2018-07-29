@extends('layouts.app')

@section('content')
    <form action="{{ route('tournaments.nextStep', $tournament->id) }}" method="post">
        {{ csrf_field() }}
        <input type="submit" name="nextStep" value="Next step" />
    </form>
    <hr>

    @foreach($tournament->divisions as $division)
        <table class="division">
            <tr>
                <th colspan="{{ $division->teams->count() + 2 }}">{{ $division->title }}</th>
            </tr>
            @foreach ($division->teams as $team)
                <tr>
                    <td>{{ $team->title }}</td>
                    @foreach($division->teams as $oponent)
                        @php
                            $game = $division->findGame($team, $oponent)
                        @endphp

                        @if( ! $game)
                            <td class="ignored"></td>
                        @elseif( ! $game->isFinished())
                            <td class="pending_game"></td>
                        @else
                            <td>{{ $game->teamScore($team) }}:{{ $game->teamScore($oponent) }}</td>
                        @endif
                    @endforeach
                    <td>{{ $team->pivot->score }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endsection

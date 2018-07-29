@extends('layouts.app')

@section('content')
    <form action="{{ route('tournaments.create') }}" method="post">
        {{ csrf_field() }}

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" placeholder="title">

        @if($errors->has('title'))
            <span class="error">{{ $errors->first('title') }}</span>
        @endif

        <input type="submit" name="Create" value="Create">
    </form>
    <hr>

    <table>
        <tr>
            <th>Name</th>
        </tr>
        @foreach($tournaments as $tournament)
            <tr>
                <td>
                    <a href="{{ route('tournaments.view', $tournament->id) }}">
                        {{ $tournament->title }}
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection

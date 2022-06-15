@extends('main')
@section('tab_title')
    Dota 2 Loot Boxes
@endsection
@section('content')
    <div class="boxes-row">
        <h4>Heroes boxes</h4>
        <div class="boxes-container">
            @foreach ($boxes as $box)
                @if ($box->category != 'heroes') @continue @endif
                <a href="{{route('show', $box->id)}}" class="box-single">
                    <div class="img-wrapper" style="background-image: url('/storage/{{$box->filepath}}')">
                    </div>
                    <span class="box--title">{{ $box->name }}</span>
                    <span class="box--price">{{ $box->price }}₴</span>
                </a>
            @endforeach
        </div>
        <h4>Boxes by rarity</h4>
        <div class="boxes-container">
            @foreach ($boxes as $box)
                @if ($box->category != 'rarity') @continue @endif
                <a href="{{route('show', $box->id)}}" class="box-single">
                    <div class="img-wrapper" style="background-image: url('/storage/{{$box->filepath}}')">
                    </div>
                    <span class="box--title">{{ $box->name }}</span>
                    <span class="box--price">{{ $box->price }}₴</span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
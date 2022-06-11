@extends('main')
@section('tab_title')
    {{ $box->name }}
@endsection
@section('content')
    <div class="box-row">
        @foreach ($box->items as $item)
            <div class="item-single">
                <div class="img-wrapper" style="background-image: url('/storage/{{$item->filepath}}')">
                </div>
                <span class="item--title">{{ $item->name }}</span>
                <span class="item--price">{{ $item->price }}</span>
            </div>
        @endforeach
    </div>
    @auth
        <button class="button btn-play" data-url="{{ route('calculate') }}" data-id="{{$box->id}}">Play for <span class="box-price">{{ $box->price }}₴</span></button>
        <div class="won-item-container">
            <h4>Congratulations! You have won:</h4>
            <div class="img-wrapper"></div>
            <span class="title"></span>
            <span class="price"></span>
            <div class="actions">
                <a href="{{ route('sellItem', 0) }}"><button class="button btn-sell">Sell it for <span class="selling-price"></span></button></a>
                <button class="button btn-keep">Keep it</button>
            </div>
        </div>
    @endauth
@endsection
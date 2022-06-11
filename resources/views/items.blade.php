@extends('main')
@section('tab_title')
    My Items
@endsection
@section('content')
    <div class="items-row">
        @if (count($user->items) > 0)
            @foreach ($user->items as $item)
                <div class="item-container">
                    <div class="img-wrapper" style="background-image: url('/storage/{{$item->filepath}}')"></div>
                    <span class="title">{{ $item->name }}</span>
                    <span class="price">{{ $item->price }}</span>
                    <div class="actions">
                        <a href="{{ route('sellItem', $item->pivot->id) }}"><button class="button btn-sell">Sell it for <span class="selling-price">{{ $item->price * 1.1 }}</span></button></a>
                    </div>
                </div>
            @endforeach
        @else
            Sorry, you don't have any items yet
        @endif
    </div>
@endsection
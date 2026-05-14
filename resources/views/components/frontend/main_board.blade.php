@foreach($items as $item)
<div class="swiper-slide">
    <a href="{{ $item['url'] }}">
        <span class="imgfit">
            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
        </span>
        <span class="txt">
            <time class="date" datetime="{{ $item['datetime'] }}">{{ $item['date'] }}</time>
            <h3>{{ $item['title'] }}</h3>
        </span>
    </a>
</div>
@endforeach
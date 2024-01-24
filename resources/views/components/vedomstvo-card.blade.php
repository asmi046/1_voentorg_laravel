<a href="{{ route('vedomstvo', $item->slug) }}" class="vedomstvo_card">
    <img src="{{ $item->img }}" alt="{{ $item->title }}">
    <p>{{ $item->title }}</p>
</a>

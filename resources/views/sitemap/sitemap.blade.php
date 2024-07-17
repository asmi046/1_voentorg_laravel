@php
    $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
{!!  $xml !!}
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	@foreach ($category as $item)
        <url>
            <loc>{{ route('category', $item->slug) }}</loc>
            <lastmod>{{ (empty($item->updated_at))?date('c'):date('c',strtotime($item->updated_at)) }}</lastmod>
            @if (!empty($item->img))
                <image:image>
                    <image:loc>{{ config("app.url").$item->img }}</image:loc>
                </image:image>
            @endif
        </url>
    @endforeach

    @foreach ($vedomstvo as $item)
        <url>
            <loc>{{ route('vedomstvo', $item->slug) }}</loc>
            <lastmod>{{ (empty($item->updated_at))?date('c'):date('c',strtotime($item->updated_at)) }}</lastmod>
            @if (!empty($item->img))
                <image:image>
                    <image:loc>{{ config("app.url").$item->img }}</image:loc>
                </image:image>
            @endif
        </url>
    @endforeach

    @foreach ($product as $item)
        <url>
            <loc>{{ route('product', $item->slug) }}</loc>
            <lastmod>{{ (empty($item->updated_at))?date('c'):date('c',strtotime($item->updated_at)) }}</lastmod>
            @if (!empty($item->img))
                <image:image>
                    <image:loc>{{ config("app.url").$item->img }}</image:loc>
                </image:image>
            @endif
        </url>
    @endforeach
</urlset>

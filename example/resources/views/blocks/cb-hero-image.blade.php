
<div class="heroimg-container">
    <img class="heroimg-image" src="{{ isset($image) ? asset($image) : '' }}" alt="{{ isset($title) ? $title : '' }}">
    <div class="heroimg-content">
        <div class="heroimg-inner container">
            <div class="heroimg-title">
                {{ isset($title) ? $title : '' }}
            </div>
            <div class="heroimg-description">
                {!! isset($description) ? $description : '' !!}
            </div>
            <div class="heroimg-actions">
                @php ($page = isset($btn_page) ? App\Models\Page::find($btn_page) : null)
                @if ($page)
                    <a href="{{ url($page->slug) }}" class="heroimg-button" style="background-color: {{ isset($btn_color) ? $btn_color : '' }};">
                        {{ $page->title }}
                    </a>
                @endif
            </div>
        </div>
        
    </div>
</div>

@push('styles')
<style>
    .heroimg-container {
        position: relative;
        height: 100vh;
    }
    .heroimg-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .heroimg-content {
        position: absolute;
        bottom: 20%;
        left: 0;
        right: 0;
        z-index: 1;
    }
    .heroimg-inner {
        border-radius: 20px;
        background: rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(7px);
        padding: 40px;
    }
    .heroimg-title {
        font-size: 40px;
        font-weight: 700;
        color: #fff;
    }
    .heroimg-description {
        color: #f1f1f1;
        margin-bottom: 20px;
    }
    .heroimg-button {
        display: inline-block;
        position: relative;
        color: #fff;
        border-radius: 10px;
        padding: 14px 30px;
        padding-right: 40px;
        overflow: hidden;
    }
    .heroimg-button::after {
        content: '>';
        opacity: 0.6;
        font-family: monospace;
        font-size: 28px;
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translate(0, -50%);
        transition: all 150ms ease-in-out;
    }
    .heroimg-button:hover::after {
        transform: translate(6px, -50%);
    }
    .heroimg-button::before {
        content: '';
        border-radius: 10px;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        transition: all 150ms ease-in-out;
        background: rgba(255, 244, 244, 0.0);
    }
    .heroimg-button:hover::before {
        background: rgba(255, 244, 244, 0.3);
    }
</style>
@endpush

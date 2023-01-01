
<div class="blockfeatures-wrapper">
    <div class="blockfeatures-heading">
        {{ isset($title) ? $title : '' }}
    </div>
    
    <div class="blockfeatures-features">
        <div class="blockfeatures-features-inner container">
            @foreach ($features ?: [] as $item)
                <div class="blockfeatures-item">
                    <div class="blockfeatures-image">
                        <img class="blockfeatures-img" src="{{ asset($item['image']) }}" />
                    </div>
                    <div class="blockfeatures-details">
                        <div class="blockfeatures-name">
                            {{ isset($item['name']) ? $item['name'] : '' }}
                        </div>
                        <div class="blockfeatures-description">
                            {!! isset($item['description']) ? $item['description'] : '' !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
    <style>
        .blockfeatures-heading {
            background-color: #05113a;
            padding: 10px;
            text-align: center;
            font-size: 36px;
            font-weight: 500;
            color: #d1d1d1;
        }
        .blockfeatures-features-inner {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 10px;
        }
        .blockfeatures-item {
            width: calc(50% - 10px);
            background: #e5e5e5;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 20px;
        }
        .blockfeatures-image {
            width: 20%;
            flex-shrink: 0;
            padding-bottom: 20%;
            position: relative;
        }
        .blockfeatures-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .blockfeatures-details {
            flex-grow: 1;
        }
        .blockfeatures-name {
            font-weight: 500;
            font-size: 20px;
            color: #000;
        }
        .blockfeatures-description {
            font-size: 16px;
            color: #252525;
        }
    </style>
@endpush

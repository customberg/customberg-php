@php
    // props: entry, attribute, push_styles, push_scripts
    if (!isset($attribute)) $attribute = 'content';
    if (!isset($push_styles)) $push_styles = 'styles';
    if (!isset($push_scripts)) $push_scripts = 'scripts';
@endphp

<div class="laraberg-wrapper" style="width:100%; margin-bottom: 10px;">
    
    <textarea id="laraberg-plugin" name="{{ $attribute }}" hidden>
        @if (isset($entry))
            {{ $entry->{$attribute} }}
        @endif
    </textarea>
    
</div>

@push($push_styles)
    <link rel="stylesheet" href="{{ asset('vendor/laraberg/css/laraberg.css') }}">
@endpush

@push($push_scripts)
    <script src="https://unpkg.com/react@16.8.6/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@16.8.6/umd/react-dom.production.min.js"></script>
    <script src="{{ asset('vendor/laraberg/js/laraberg.js') }}"></script>
    @vite('resources/js/app.js')
    <script>
        jQuery(document).ready(function(){
            Laraberg.init('laraberg-plugin');
            // Laraberg.init('laraberg-plugin', { laravelFilemanager: { prefix: '/admin/laraberg-filemanager' } });
            {!! load_tm_blocks() !!}
        });
    </script>
@endpush
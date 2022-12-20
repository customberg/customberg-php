@php
    // props: entry, attribute, push_styles, push_scripts
    $config = Customberg\PHP\Customberg::getConfig();
    if (!isset($attribute)) {
        $attribute = 'content';
    }
    if (!isset($push_styles) || !$push_styles) {
        $push_styles = $config['assets']['stack_styles'];
    }
    if (!isset($push_styles) || !$push_styles) {
        $push_styles = 'styles';
    }
    if (!isset($push_scripts) || !$push_scripts) {
        $push_scripts = $config['assets']['stack_scripts'];
    }
    if (!isset($push_scripts) || !$push_scripts) {
        $push_scripts = 'scripts';
    }
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
    <link rel="stylesheet" href="{{ asset('vendor/customberg/style.css') }}">
@endpush

@push($push_scripts)
    {{-- <script src="https://unpkg.com/react@16.8.6/umd/react.production.min.js"></script> --}}
    {{-- <script src="https://unpkg.com/react-dom@16.8.6/umd/react-dom.production.min.js"></script> --}}
    
    <script src="https://unpkg.com/react@17.0.2/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@17.0.2/umd/react-dom.production.min.js"></script>

    <script src="{{ asset('vendor/laraberg/js/laraberg.js') }}"></script>
    <script src="{{ asset('vendor/customberg/customberg.umd.js') }}"></script>
    
    <script>
        jQuery(document).ready(function(){
            // Laraberg.init('laraberg-plugin', { laravelFilemanager: { prefix: '/admin/laraberg-filemanager' } });
            {!! \Customberg\PHP\Customberg::loadBlocks() !!}
            Laraberg.init('laraberg-plugin');
        });
    </script>
@endpush
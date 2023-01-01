@php
    // props: entry, attribute, push_styles, push_scripts
    $config = Customberg\PHP\Customberg::getConfig();
    try {
        $version = \Composer\InstalledVersions::getVersion('customberg/customberg-php') . '@' .  \Composer\InstalledVersions::getReference('customberg/customberg-php');
        $versionLar = \Composer\InstalledVersions::getVersion('van-ons/laraberg') . '@' .  \Composer\InstalledVersions::getReference('van-ons/laraberg');
    } catch (\Exception $e) {
        $version = '0.0.0';
        $versionLar = '0.0.0';
    }
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
    $custombergConfig = [
        'routes_preview' => route('customberg.preview'),
        'routes_file_upload' => route('customberg.file_upload'),
        'default_language' => $config['default_language'] ?: app()->getLocale(),
        'languages' => $config['languages'],
    ];
@endphp

<div class="laraberg-wrapper" style="width:100%; margin-bottom: 10px;">
    
    <textarea id="laraberg-plugin" name="{{ $attribute }}" hidden>
        @if (isset($entry))
            {{ $entry->{$attribute} }}
        @endif
    </textarea>
    
</div>

@push($push_styles)
    <link rel="stylesheet" href="{{ asset('vendor/laraberg/css/laraberg.css') }}?ver={{ $versionLar }}">
    <link rel="stylesheet" href="{{ asset('vendor/customberg/style.css') }}?ver={{ $version }}">
@endpush

@push($push_scripts)
    <script src="https://unpkg.com/react@17.0.2/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@17.0.2/umd/react-dom.production.min.js"></script>

    <script src="{{ asset('vendor/laraberg/js/laraberg.js') }}?ver={{ $versionLar }}"></script>
    <script src="{{ asset('vendor/customberg/customberg.umd.js') }}?ver={{ $version }}"></script>
    
    <script>
        jQuery(document).ready(function(){
            window.CustombergConfig = @json($custombergConfig);
            {!! \Customberg\PHP\Customberg::loadBlocks() !!}
            Laraberg.init('laraberg-plugin', {
                height: 1400,
                mediaUpload: function (upload) {
                    window.CustombergUploadAction(upload.filesList)
                    .then(function (data) {
                        var files = [];
                        for (var i = 0; i < data.length; i++) {
                            var name = (''+data[i]).split('/').pop();
                            files.push({
                                id: '',// + Date.now() + data[i],
                                name: name,
                                url: data[i],
                            })
                        }
                        upload.onFileChange(files);
                    })
                    .catch(function (error) {
                        upload.onError(error.message)
                    });
                },
            });
        });
    </script>
@endpush
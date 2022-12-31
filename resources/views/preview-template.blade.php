<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <style>
            *, :after, :before {
                box-sizing: border-box;
            }
        </style>
        @stack('styles')
    </head>
    
    {{--
        If you want a preview closer to the live website,
        publish and customize this view file to add libraries and global css
    --}}
    <body>
        
        @yield('main')
        
        @stack('scripts')
    </body>
</html>

{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
{{-- <li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}
    </a>
</li> --}}

<li class="nav-item">
    <a class="nav-link" href="{{ url('/page') }}">
        Page Admin
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ url('/homepage') }}">
        Homepage demo
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ url('/about') }}">
        About us
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ url('/contact') }}">
        Contact
    </a>
</li>

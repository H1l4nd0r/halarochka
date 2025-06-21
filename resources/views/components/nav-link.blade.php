@props(['active' => false])
<li class="nav-item">
    <a class="nav-link {{ $active?'active':'' }}" aria-current="{{ $active?'page':'false' }}" :active="$request()->is('/')" {{ $attributes }}>{{ $slot }}</a>
</li>
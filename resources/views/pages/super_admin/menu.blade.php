{{--Manage Settings--}}
<li class="nav-item">
    <a href="{{ route('settings.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['settings.index', 'settings.update']) ? 'active' : '' }}"><i class="icon-gear"></i> <span>Settings</span></a>
</li>

{{--Manage Branches--}}
<li class="nav-item">
    <a href="{{ route('branches.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['branches.index', 'branches.create', 'branches.edit', 'branches.show']) ? 'active' : '' }}"><i class="icon-office"></i> <span>Branches</span></a>
</li>

{{--Pins--}}
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['pins.create', 'pins.index']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-lock2"></i> <span> Pins</span></a>

    <ul class="nav nav-group-sub" data-submenu-title="Manage Pins">
        {{--Generate Pins--}}
            <li class="nav-item">
                <a href="{{ route('pins.create') }}"
                   class="nav-link {{ (Route::is('pins.create')) ? 'active' : '' }}">Generate Pins</a>
            </li>

        {{--    Valid/Invalid Pins  --}}
        <li class="nav-item">
            <a href="{{ route('pins.index') }}"
               class="nav-link {{ (Route::is('pins.index')) ? 'active' : '' }}">View Pins</a>
        </li>
    </ul>
</li>

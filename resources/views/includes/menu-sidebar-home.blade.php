<style>
.floating-menu {
  background-color: #c30000; 
  border-radius: 5px;
  padding: 15px 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  max-width: 240px;
  z-index: 999;
}

.floating-menu a {
  color: white;
  display: flex;
  align-items: center;
  padding: 8px 10px;
  border-radius: 4px;
  transition: background 0.2s;
  text-decoration: none;
}

.floating-menu a:hover {
  background-color: #a10000;
}

.floating-menu a.active {
  background-color: #a10000;
  pointer-events: none;
}
</style>

<ul class="list-unstyled d-lg-block d-none menu-left-home sticky-top floating-menu">
	<li>
		<a href="{{url('/')}}" @if (request()->is('/')) class="active disabled" @endif>
			<i class="bi-house-door"></i>
			<span class="ml-2">{{ __('admin.home') }}</span>
		</a>
	</li>

	@if ($settings->allow_reels && auth()->user()?->getReelsActive() || $settings->allow_reels && auth()->guest())
	<li>
		<a href="{{ url('reels') }}" @guest data-toggle="modal" data-target="#loginFormModal" @endguest>
			<svg xmlns="http://www.w3.org/2000/svg" class="align-text-bottom me-2" fill="currentColor" width="19" height="19" viewBox="0 0 50 50">
				<path d="...svg path..." stroke="currentColor" stroke-width="3" fill="none"></path>
			</svg>
			<span class="ml-2">{{ __('general.reels') }}</span>
		</a>
	</li>
	@endif

	@auth
	<li>
		<a href="{{ url(auth()->user()->username) }}">
			<i class="bi-person"></i>
			<span class="ml-2">{{ auth()->user()->verified_id == 'yes' ? __('general.my_page') : __('users.my_profile') }}</span>
		</a>
	</li>
	@if (auth()->user()->verified_id == 'yes')
	<li>
		<a href="{{ url('dashboard') }}">
			<i class="bi-speedometer2"></i>
			<span class="ml-2">{{ __('admin.dashboard') }}</span>
		</a>
	</li>
	@endif
	<li>
		<a href="{{ url('my/purchases') }}" @if (request()->is('my/purchases')) class="active disabled" @endif>
			<i class="bi-bag-check"></i>
			<span class="ml-2">{{ __('general.purchased') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url('messages') }}">
			<i class="feather icon-send"></i>
			<span class="ml-2">{{ __('general.messages') }}</span>
		</a>
	</li>
	@if (!$settings->disable_explore_section)
	<li>
		<a href="{{ url('explore') }}" @if (request()->is('explore')) class="active disabled" @endif>
			<i class="bi-compass"></i>
			<span class="ml-2">{{ __('general.explore') }}</span>
		</a>
	</li>
	@endif
	<li>
		<a href="{{ url('my/subscriptions') }}">
			<i class="bi-person-check"></i>
			<span class="ml-2">{{ __('admin.subscriptions') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url('my/bookmarks') }}" @if (request()->is('my/bookmarks')) class="active disabled" @endif>
			<i class="bi-bookmark"></i>
			<span class="ml-2">{{ __('general.bookmarks') }}</span>
		</a>
	</li>

	@else
	<li>
		<a href="{{ url('creators') }}">
			<i class="bi-compass"></i>
			<span class="ml-2">{{ __('general.explore') }}</span>
		</a>
	</li>

	@if ($settings->shop)
	<li>
		<a href="{{ url('shop') }}">
			<i class="feather icon-shopping-bag"></i>
			<span class="ml-2">{{ __('general.shop') }}</span>
		</a>
	</li>
	@endif

	@endauth
</ul>

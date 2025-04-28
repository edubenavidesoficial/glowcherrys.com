<ul class="list-unstyled d-lg-block d-none menu-left-home sticky-top">
	<li>
		<a href="{{url('/')}}" @if (request()->is('/')) class="active disabled" @endif>
			<i class="bi bi-house-door"></i>
			<span class="ml-2">{{ trans('admin.home') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url(auth()->user()->username) }}">
			<i class="bi bi-person"></i>
			<span class="ml-2">{{ auth()->user()->verified_id == 'yes' ? trans('general.my_page') : trans('users.my_profile') }}</span>
		</a>
	</li>
	@if (auth()->user()->verified_id == 'yes')
	<li>
		<a href="{{ url('dashboard') }}">
			<i class="bi bi-speedometer2"></i>
			<span class="ml-2">{{ trans('admin.dashboard') }}</span>
		</a>
	</li>
	@endif
	<li>
		<a href="{{ url('my/purchases') }}" @if (request()->is('my/purchases')) class="active disabled" @endif>
			<i class="bi bi-bag-check"></i>
			<span class="ml-2">{{ trans('general.purchased') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url('messages') }}">
			<i class="feather icon-send"></i>
			<span class="ml-2">{{ trans('general.messages') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url('explore') }}" @if (request()->is('explore')) class="active disabled" @endif>
			<i class="bi bi-compass"></i>
			<span class="ml-2">{{ trans('general.explore') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url('my/subscriptions') }}">
			<i class="bi bi-person-check"></i>
			<span class="ml-2">{{ trans('admin.subscriptions') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url('my/bookmarks') }}" @if (request()->is('my/bookmarks')) class="active disabled" @endif>
			<i class="bi bi-bookmark"></i>
			<span class="ml-2">{{ trans('general.bookmarks') }}</span>
		</a>
	</li>
	<li>
		@if ($settings->live_streaming_status == 'on')
			<button type="button" data-toggle="tooltip" data-placement="top" title="{{trans('general.stream_live')}}" class="btn btn-upload p-bottom-8 btn-tooltip-form e-none align-bottom btnCreateLive @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill">
				<i class="bi bi-broadcast f-size-25"></i>
				<span class="ml-1">{{ trans('general.stream_live_home') }}</span>
			</button>
		@endif
	</li>
</ul>

<style>
.menu-left-home {
    font-size: 1rem;
    font-weight: 500;
    background: rgba(247, 160, 179, 0.315);
    padding: 5px;
    border-radius: 10px;
    overflow: hidden;
    backdrop-filter: blur(10px); /* efecto vidrio */
    -webkit-backdrop-filter: blur(10px); /* soporte Safari */
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2); /* sombra suave */
}

/* Efecto de "zoom-in" en íconos */
.menu-left-home a i {
    font-size: 1.2rem;
    color: #ff0000;
    transition: transform 0.3s ease, color 0.3s ease;
}

/* Animación al pasar el mouse */
.menu-left-home a {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    border-radius: 8px;
    color: #333;
    transition: background 0.3s ease-in-out, transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    text-decoration: none;
}

/* Espaciado mejorado */
.menu-left-home a span {
    margin-left: 15px;
}

/* Efecto al pasar el mouse */
.menu-left-home a:hover {
    background: #ff0000;
    color: #fff;
    transform: translateY(-5px); /* efecto flotante hacia arriba */
    box-shadow: 0 10px 20px rgba(255, 0, 0, 0.4); /* sombra al flotar */
}

/* Ícono crece al pasar el mouse */
.menu-left-home a:hover i {
    color: #fff;
    transform: scale(1.2);
}

/* Estilo para enlace activo */
.menu-left-home a.active {
    background: #b30000;
    color: #fff;
    pointer-events: none;
    opacity: 0.7;
    transform: scale(1.05); /* Leve agrandamiento */
}
</style>

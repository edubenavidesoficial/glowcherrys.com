<header>

    <style>
        .balance-external {
            padding-right: 1.5%;
            font-size: 1.6rem;
            color: #ff0000;
        }

        @media(max-width: 767.98px) {

            #logo-image-mobile,
            #logo-image-mobile-logged,
            #button-menu-burger {
                top: -25px;
                position: relative;

            }

            #navbar-menu-mobile {

                height: 10% !important
            }

            .buttons-mobile-nav {

                top: 18px !important;
            }
            .balance-external {
            padding-right: 3rem;
            padding-bottom: 6%;
            font-size: 1.5rem;
            color: #ff0000;
            margin-top: -7px; 
        }

        }

        @media(min-width: 768px) {
            #navbar-menu-mobile {
                height: 11% !important
            }
        }
        #navbar-menu-mobile #navbar
       en pantalla normal  quiero que sea height: 10% !important

        .ico {
            width: 20px;
        }

        .icoo {
            width: 24px;
        }
    </style>

    <nav id="navbar-menu-mobile"
        class="navbar navbar-expand-lg navbar-inverse fixed-top p-nav @if (auth()->guest() && request()->path() == '/') scroll @else p-3 @if (request()->is('live/*')) d-none @endif  @if (request()->is('messages/*')) d-none d-lg-block shadow-sm @elseif(request()->is('messages')) shadow-sm @else shadow-custom @endif {{ auth()->check() && auth()->user()->dark_mode == 'on' ? 'bg-white' : 'navbar_background_color' }} link-scroll @endif">
        <div class="container-fluid d-flex position-relative">
            @auth
            <div class="buttons-mobile-nav d-lg-none">     
                <div class="balance-external">
                    <a href="/my/wallet" class="d-flex">
                               @if (!is_null(auth()->user()))
                                    {{ Helper::amountFormatDecimal(auth()->user()->balance) }}
                                @endif
                            </a> 
                </div>  
            </div> 
                <div class="buttons-mobile-nav d-lg-none">     
                    <a class="btn-mobile-nav navbar-toggler-mobile" href="#" data-toggle="collapse"
                        data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" role="button">
                        <i class="feather icon-menu icon-navbar"></i>
                    </a>
                </div>
            @endauth

            <a class="navbar-brand" href="{{ url('/') }}">
                @if (auth()->check() && auth()->user()->dark_mode == 'on')
                    <img id="logo-image-mobile-logged" src="{{ url('/img', $settings->logo) }}"
                        data-logo="{{ $settings->logo }}" data-logo-2="{{ $settings->logo_2 }}"
                        alt="{{ $settings->title }}" class="logo align-bottom max-w-100" />
                @else
                    <img id="logo-image-mobile"
                        src="{{ url('/img', auth()->guest() && request()->path() == '/' ? $settings->logo : $settings->logo_2) }}"
                        data-logo="{{ $settings->logo }}" data-logo-2="{{ $settings->logo_2 }}"
                        alt="{{ $settings->title }}" class="logo align-bottom max-w-100" />
                @endif
            </a>

            @guest
                <button id="button-menu-burger" class="navbar-toggler @if (auth()->guest() && request()->path() == '/') text-white @endif"
                    type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
            @endguest

            <div class="collapse navbar-collapse navbar-mobile" id="navbarCollapse">
                <div class="d-lg-none text-right pr-2 mb-2">
                    <button type="button" class="navbar-toggler close-menu-mobile" data-toggle="collapse"
                        data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                @if ((auth()->guest() && $settings->who_can_see_content == 'all') || auth()->check())

                    <ul class="navbar-nav mr-auto">

                        @if (!$settings->disable_search_creators)
                            <form class="form-inline my-lg-0 position-relative" method="get"
                                action="{{ url('creators') }}">
                                <input id="searchCreatorNavbar"
                                    class="form-control search-bar @if (auth()->guest() && request()->path() == '/') border-0 @endif"
                                    type="text" required name="q" autocomplete="off" minlength="3"
                                    placeholder="{{ trans('general.find_user') }}" aria-label="Search">
                                <button class="btn btn-outline-success my-sm-0 button-search e-none" type="submit"><i
                                        class="bi bi-search"></i></button>

                                <div class="dropdown-menu dd-menu-user position-absolute" style="width: 95%; top: 48px;"
                                    id="dropdownCreators">

                                    <button type="button" class="d-none" id="triggerBtn" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"></button>

                                    <div class="w-100 text-center display-none py-2" id="spinnerSearch">
                                        <span class="spinner-border spinner-border-sm align-middle text-primary"></span>
                                    </div>

                                    <div id="containerCreators"></div>
                                    <div id="viewAll" class="display-none mt-2">
                                        <a class="dropdown-item border-top py-2 text-center"
                                            href="#">{{ __('general.view_all') }}</a>
                                    </div>
                                </div><!-- dropdown-menu -->
                            </form>
                        @endif

                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('creators') }}">{{ trans('general.explore') }}</a>
                            </li>

                            @if ($settings->shop)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('shop') }}">{{ trans('general.shop') }}</a>
                                </li>
                            @endif
                        @endguest

                    </ul>
                @endif

                <ul class="navbar-nav ml-auto">
                    @guest
                        <li class="nav-item mr-1">
                            <a @if (Helper::showLoginFormModal()) data-toggle="modal" data-target="#loginFormModal" @endif
                                class="nav-link login-btn @if ($settings->registration_active == '0') btn btn-main btn-primary pr-3 pl-3 @endif"
                                href="{{ $settings->home_style == 0 ? url('login') : url('/') }}">
                                {{ trans('auth.login') }}
                            </a>
                        </li>

                        @if ($settings->registration_active == '1')
                            <li class="nav-item">
                                <a @if (Helper::showLoginFormModal()) data-toggle="modal" data-target="#loginFormModal" @endif
                                    class="toggleRegister nav-link btn btn-main btn-primary pr-3 pl-3 btn-arrow btn-arrow-sm"
                                    href="{{ $settings->home_style == 0 ? url('signup') : url('/') }}">
                                    {{ trans('general.getting_started') }}
                                </a>
                            </li>
                        @endif
                    @else
                        <!-- ============ Menu Mobile ============-->
                        @if (auth()->user()->role == 'admin')
                            <li class="nav-item dropdown d-lg-none mt-2 border-bottom">
                                <a href="{{ url('panel/admin') }}" class="nav-link px-2 link-menu-mobile py-1">
                                    <div>
                                        <i class="bi bi-speedometer2 mr-2"></i>
                                        <span class="d-lg-none">{{ trans('admin.admin') }}</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown d-lg-none @if (auth()->user()->role != 'admin') mt-2 @endif">
                            <a href="{{ url(auth()->user()->username) }}"
                                class="nav-link px-2 link-menu-mobile py-1 url-user">
                                <div>
                                    <img src="{{ Helper::getFile(config('path.avatar') . auth()->user()->avatar) }}"
                                        alt="User" class="rounded-circle avatarUser mr-1" width="20"
                                        height="20">
                                    <span
                                        class="d-lg-none">{{ auth()->user()->verified_id == 'yes' ? trans('general.my_page') : trans('users.my_profile') }}</span>
                                </div>
                            </a>
                        </li>

                        @if (auth()->user()->verified_id == 'yes')
                            <li class="nav-item dropdown d-lg-none">
                                <a href="{{ url('dashboard') }}" class="nav-link px-2 link-menu-mobile py-1">
                                    <div>
                                        <i class="bi bi-speedometer2 mr-2"></i>
                                        <span class="d-lg-none">{{ trans('admin.dashboard') }}</span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item dropdown d-lg-none">
                                <a href="{{ url('my/posts') }}" class="nav-link px-2 link-menu-mobile py-1">
                                    <div>
                                        <i class="feather icon-feather mr-2"></i>
                                        <span class="d-lg-none">{{ trans('general.my_posts') }}</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown d-lg-none">
                            <a href="{{ url('my/bookmarks') }}" class="nav-link px-2 link-menu-mobile py-1">
                                <div>
                                    <i class="feather icon-bookmark mr-2"></i>
                                    <span class="d-lg-none">{{ trans('general.bookmarks') }}</span>
                                </div>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-lg-none border-bottom">
                            <a href="{{ url('my/likes') }}" class="nav-link px-2 link-menu-mobile py-1">
                                <div>
                                    <i class="feather icon-heart mr-2"></i>
                                    <span class="d-lg-none">{{ trans('general.likes') }}</span>
                                </div>
                            </a>
                        </li>

                        @if (auth()->user()->verified_id == 'yes' || $settings->referral_system == 'on' || auth()->user()->balance != 0.0)
                            <li class="nav-item dropdown d-lg-none">
                                <a class="nav-link px-2 link-menu-mobile py-1 balance">
                                    <div>
                                        <i class="iconmoon icon-Dollar mr-2"></i>
                                        <span class="d-lg-none balance">{{ trans('general.balance') }}:
                                            {{ Helper::amountFormatDecimal(auth()->user()->balance) }}</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        @if (($settings->disable_wallet == 'on' && auth()->user()->wallet != 0.0) || $settings->disable_wallet == 'off')
                            <li class="nav-item dropdown d-lg-none border-bottom">
                                <a @if ($settings->disable_wallet == 'off') href="{{ url('my/wallet') }}" @endif
                                    class="nav-link px-2 link-menu-mobile py-1">
                                    <div>
                                        <i class="iconmoon icon-Wallet mr-2"></i>
                                        {{ trans('general.wallet') }}: <span
                                            class="balanceWallet">{{ Helper::userWallet() }}</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->verified_id == 'yes')
                            <li class="nav-item dropdown d-lg-none">
                                <a href="{{ url('my/subscribers') }}" class="nav-link px-2 link-menu-mobile py-1">
                                    <div>
                                        <i class="feather icon-users mr-2"></i>
                                        <span class="d-lg-none">{{ trans('users.my_subscribers') }}</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown d-lg-none">
                            <a href="{{ url('my/subscriptions') }}" class="nav-link px-2 link-menu-mobile py-1">
                                <div>
                                    <i class="feather icon-user-check mr-2"></i>
                                    <span class="d-lg-none">{{ trans('users.my_subscriptions') }}</span>
                                </div>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-lg-none border-bottom">
                            <a href="{{ url('my/purchases') }}" class="nav-link px-2 link-menu-mobile py-1">
                                <div>
                                    <i class="bi bi-bag-check mr-2"></i>
                                    <span class="d-lg-none">{{ trans('general.purchased') }}</span>
                                </div>
                            </a>
                        </li>

                        @if (auth()->user()->verified_id == 'no' && auth()->user()->verified_id != 'reject')
                            <li class="nav-item dropdown d-lg-none">
                                <a href="{{ url('settings/verify/account') }}"
                                    class="nav-link px-2 link-menu-mobile py-1">
                                    <div>
                                        <i class="feather icon-star mr-2"></i>
                                        <span class="d-lg-none">{{ trans('general.become_creator') }}</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown d-lg-none">
                            <a href="{{ auth()->user()->dark_mode == 'off' ? url('mode/dark') : url('mode/light') }}"
                                class="nav-link px-2 link-menu-mobile py-1">
                                <div>
                                    <i
                                        class="feather icon-{{ auth()->user()->dark_mode == 'off' ? 'moon' : 'sun' }} mr-2"></i>
                                    <span
                                        class="d-lg-none">{{ auth()->user()->dark_mode == 'off' ? trans('general.dark_mode') : trans('general.light_mode') }}</span>
                                </div>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-lg-none mb-2">
                            <a href="{{ url('logout') }}" class="nav-link px-2 link-menu-mobile py-1">
                                <div>
                                    <i class="feather icon-log-out mr-2"></i>
                                    <span class="d-lg-none">{{ trans('auth.logout') }}</span>
                                </div>
                            </a>
                        </li>
                        <!-- =========== End Menu Mobile ============-->


                        <li class="nav-item dropdown d-lg-block d-none">
                            <a class="nav-link px-2" href="{{ url('/') }}" title="{{ trans('admin.home') }}">
                                <img src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/home.svg') : asset('img/icons/home-light.png') }}"
                                    alt="{{ trans('admin.home') }}" class="ico">
                                <span class="d-lg-none align-middle ml-1">{{ trans('admin.home') }}</span>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-lg-block d-none">
                            <a class="nav-link px-2" href="{{ url('creators') }}"
                                title="{{ trans('general.explore_creators') }}">
                                <img src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/compass.svg') : asset('img/icons/compass-light.png') }}"
                                    alt="{{ trans('admin.home') }}" class="ico">
                                <span class="d-lg-none align-middle ml-1">{{ trans('general.explore') }}</span>
                            </a>
                        </li>

                        @if ($settings->shop)
                            <li class="nav-item dropdown d-lg-block d-none">
                                <a class="nav-link px-2" href="{{ url('shop') }}"
                                    title="{{ trans('general.shop') }}">
                                    <img src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/bag.png') : asset('img/icons/bag-light.png') }}"
                                        alt="{{ trans('admin.home') }}" class="icoo">
                                    <span class="d-lg-none align-middle ml-1">{{ trans('general.shop') }}</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown d-lg-block d-none">
                            <a href="{{ url('messages') }}" class="nav-link px-2"
                                title="{{ trans('general.messages') }}">

                                <span class="noti_msg notify @if (auth()->user()->messagesInbox() != 0) d-block @endif">
                                    {{ auth()->user()->messagesInbox() }}
                                </span>

                                <img src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/paper.svg') : asset('img/icons/paper-light.png') }}"
                                    alt="{{ trans('admin.home') }}" class="ico">
                                <span class="d-lg-none align-middle ml-1">{{ trans('general.messages') }}</span>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-lg-block d-none">
                            <a href="{{ url('notifications') }}" class="nav-link px-2"
                                title="{{ trans('general.notifications') }}">

                                <span class="noti_notifications notify @if (auth()->user()->unseenNotifications()) d-block @endif">
                                    {{ auth()->user()->unseenNotifications() }}
                                </span>

                                <img src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/bell.svg') : asset('img/icons/bell-light.png') }}"
                                    alt="{{ trans('admin.home') }}" class="ico">
                                <span class="d-lg-none align-middle ml-1">{{ trans('general.notifications') }}</span>
                            </a>
                        </li>

                        <li class="nav-item dropdown d-lg-block d-none">
                            <a class="nav-link" href="#" id="nav-inner-success_dropdown_1" role="button"
                                data-toggle="dropdown">
                                <img src="{{ Helper::getFile(config('path.avatar') . auth()->user()->avatar) }}"
                                    alt="User" class="rounded-circle avatarUser mr-1" width="28" height="28">
                                <span class="d-lg-none">{{ auth()->user()->first_name }}</span>
                                <i class="feather icon-chevron-down m-0 align-middle"></i>
                            </a>
                            <div class="dropdown-menu mb-1 dropdown-menu-right dd-menu-user"
                                aria-labelledby="nav-inner-success_dropdown_1">
                                @if (auth()->user()->role == 'admin')
                                    <a class="dropdown-item dropdown-navbar" href="{{ url('panel/admin') }}"><i
                                            class="bi bi-speedometer2 mr-2"></i> {{ trans('admin.admin') }}</a>
                                    <div class="dropdown-divider"></div>
                                @endif

                                @if (auth()->user()->verified_id == 'yes' || $settings->referral_system == 'on' || auth()->user()->balance != 0.0)
                                    <span class="dropdown-item dropdown-navbar balance">
                                        <i class="iconmoon icon-Dollar mr-2"></i> {{ trans('general.balance') }}:
                                        {{ Helper::amountFormatDecimal(auth()->user()->balance) }}
                                    </span>
                                @endif

                                @if (($settings->disable_wallet == 'on' && auth()->user()->wallet != 0.0) || $settings->disable_wallet == 'off')
                                    @if ($settings->disable_wallet == 'off')
                                        <a class="dropdown-item dropdown-navbar" href="{{ url('my/wallet') }}">
                                            <i class="iconmoon icon-Wallet mr-2"></i> {{ trans('general.wallet') }}:
                                            <span class="balanceWallet">{{ Helper::userWallet() }}</span>
                                        </a>
                                    @else
                                        <span class="dropdown-item dropdown-navbar balance">
                                            <i class="iconmoon icon-Wallet mr-2"></i> {{ trans('general.wallet') }}:
                                            <span class="balanceWallet">{{ Helper::userWallet() }}</span>
                                        </span>
                                    @endif

                                    <div class="dropdown-divider"></div>
                                @endif

                                @if ($settings->disable_wallet == 'on' && auth()->user()->verified_id == 'yes')
                                    <div class="dropdown-divider"></div>
                                @endif

                                <a class="dropdown-item dropdown-navbar url-user"
                                    href="{{ url(auth()->User()->username) }}"><i class="feather icon-user mr-2"></i>
                                    {{ auth()->user()->verified_id == 'yes' ? trans('general.my_page') : trans('users.my_profile') }}</a>
                                @if (auth()->user()->verified_id == 'yes')
                                    <a class="dropdown-item dropdown-navbar" href="{{ url('dashboard') }}"><i
                                            class="bi bi-speedometer2 mr-2"></i> {{ trans('admin.dashboard') }}</a>
                                    <a class="dropdown-item dropdown-navbar" href="{{ url('my/posts') }}"><i
                                            class="feather icon-feather mr-2"></i> {{ trans('general.my_posts') }}</a>
                                @endif

                                <div class="dropdown-divider"></div>
                                @if (auth()->user()->verified_id == 'yes')
                                    <a class="dropdown-item dropdown-navbar" href="{{ url('my/subscribers') }}"><i
                                            class="feather icon-users mr-2"></i> {{ trans('users.my_subscribers') }}</a>
                                @endif
                                <a class="dropdown-item dropdown-navbar" href="{{ url('my/subscriptions') }}"><i
                                        class="feather icon-user-check mr-2"></i>
                                    {{ trans('users.my_subscriptions') }}</a>
                                <a class="dropdown-item dropdown-navbar" href="{{ url('my/bookmarks') }}"><i
                                        class="feather icon-bookmark mr-2"></i> {{ trans('general.bookmarks') }}</a>
                                <a class="dropdown-item dropdown-navbar" href="{{ url('my/likes') }}"><i
                                        class="feather icon-heart mr-2"></i> {{ trans('general.likes') }}</a>

                                @if (auth()->user()->verified_id == 'no' &&
                                        auth()->user()->verified_id != 'reject' &&
                                        $settings->requests_verify_account == 'on')
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item dropdown-navbar"
                                        href="{{ url('settings/verify/account') }}"><i
                                            class="feather icon-star mr-2"></i> {{ trans('general.become_creator') }}</a>
                                @endif

                                <div class="dropdown-divider"></div>

                                @if (auth()->user()->dark_mode == 'off')
                                    <a class="dropdown-item dropdown-navbar" href="{{ url('mode/dark') }}"><i
                                            class="feather icon-moon mr-2"></i> {{ trans('general.dark_mode') }}</a>
                                @else
                                    <a class="dropdown-item dropdown-navbar" href="{{ url('mode/light') }}"><i
                                            class="feather icon-sun mr-2"></i> {{ trans('general.light_mode') }}</a>
                                @endif

                                <div class="dropdown-divider dropdown-navbar"></div>
                                <a class="dropdown-item dropdown-navbar" href="{{ url('logout') }}"><i
                                        class="feather icon-log-out mr-2"></i> {{ trans('auth.logout') }}</a>
                            </div>
                        </li>
                        <div class="balance-external d-none d-md-block dropdown">
                            <a href="#" id="nav-inner-success_dropdown_4" role="button"
                            data-toggle="dropdown" class="d-flex">
                               @if (!is_null(auth()->user()))
                                    {{ Helper::amountFormatDecimal(auth()->user()->balance) }}
                                @endif
                            </a> 
                            <div class="dropdown-menu dropdown-menu-end dd-menu-user p-3" 
                            aria-labelledby="nav-inner-success_dropdown_4"
                            style="min-width: 250px; left: -140%;">
                       
                           <div class="wallet-option d-flex justify-content-between align-items-center mb-3">
                               <span>$10 Wallet Balance</span>
                               <a href="/my/wallet" class="btn btn-sm btn-outline-primary px-3">$10</a>
                           </div>
                       
                           <div class="wallet-option d-flex justify-content-between align-items-center mb-3">
                               <span>$25 Wallet Balance</span>
                               <a href="/my/wallet" class="btn btn-sm btn-outline-primary px-3">$25</a>
                           </div>
                       
                           <div class="wallet-option d-flex justify-content-between align-items-center mb-3">
                               <span>$50 Wallet Balance</span>
                               <a href="/my/wallet" class="btn btn-sm btn-outline-primary px-3">$50</a>
                           </div>
                       
                           <div class="wallet-option d-flex justify-content-between align-items-center mb-3">
                               <span>$100 Wallet Balance</span>
                               <a href="/my/wallet" class="btn btn-sm btn-outline-primary px-3">$100</a>
                           </div>
                       
                           <div class="wallet-option d-flex justify-content-between align-items-center">
                               <span>$500 Wallet Balance</span>
                               <a href="/my/wallet" class="btn btn-sm btn-outline-primary px-3">$500</a>
                           </div>
                       </div>
                       
                        </div>                        

                        <li class="nav-item d-flex align-items-center">
                            
                            <!-- Botón redondo principal de idiomas -->
                            <a href="#" id="nav-inner-success_dropdown_2" role="button"
                            data-toggle="dropdown" class="nav-link rounded-circle d-flex justify-content-center align-items-center"
                               style="width: 40px; height: 40px; background-color: #000000; border: 2px solid #ffffff;">
                               <i class="fas fa-flag text-white"></i> <!-- Ícono de bandera blanco -->
                            </a>
                            <div class="dropdown-menu mb-1 dropdown-menu-right dd-menu-user"
                            aria-labelledby="nav-inner-success_dropdown_2"> 

                            <select name="language" class="form-control custom-select">
                                <option @if (auth()->user()->language == '') selected="selected" @endif value="">({{trans('general.language')}}) {{ __('general.not_specified') }}</option>
                                @foreach (Languages::orderBy('name')->get() as $languages)
                                  <option @if (auth()->user()->language == $languages->abbreviation) selected="selected" @endif value="{{$languages->abbreviation}}">{{ $languages->name }}</option>
                                @endforeach
                                </select>
                        </div>

                            <!-- Botón redondo principal -->
                            <a href="{{ url('settings/page') }}" 
                               class="nav-link btn btn-primary rounded-circle d-flex justify-content-center align-items-center"
                               style="width: 40px; height: 40px;">
                                <i class="fas fa-cog"></i> <!-- Puedes cambiar el ícono si deseas -->
                            </a>
                        
                        </li>                        

                    @endguest

                </ul>
            </div>
        </div>
    </nav>
</header>

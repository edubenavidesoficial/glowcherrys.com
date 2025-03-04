<style>
    .ico {
        width: 20px;
    }

    .icoo {
        width: 24px;
    }
</style>
<div class="menuMobile w-100 bg-white shadow-lg p-3 border-top">
    <ul class="list-inline d-flex bd-highlight m-0 text-center">

        <li class="flex-fill bd-highlight">
            <a class="p-3 btn-mobile" href="{{url('/')}}" title="{{trans('admin.home')}}">
                <img
                    src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/home.svg') : asset('img/icons/home-light.png') }}"
                    alt="{{ trans('admin.home') }}"
                    class="ico">
            </a>
        </li>

        <li class="flex-fill bd-highlight">
            <img
                src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/compass.svg') : asset('img/icons/compass-light.png') }}"
                alt="{{ trans('admin.home') }}"
                class="ico">
        </li>

        @if ($settings->shop)
        <li class="flex-fill bd-highlight">
            <a class="p-3 btn-mobile" href="{{url('shop')}}" title="{{trans('general.shop')}}">
                <img
                    src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/bag.png') : asset('img/icons/bag-light.png') }}"
                    alt="{{ trans('admin.home') }}"
                    class="icoo">
            </a>
        </li>
        @endif

        <li class="flex-fill bd-highlight">
            <a href="{{url('messages')}}" class="p-3 btn-mobile position-relative" title="{{ trans('general.messages') }}">

                <span class="noti_msg notify @if (auth()->user()->messagesInbox() != 0) d-block @endif">
                    {{ auth()->user()->messagesInbox() }}
                </span>

                <img
                    src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/paper.svg') : asset('img/icons/paper-light.png') }}"
                    alt="{{ trans('admin.home') }}"
                    class="ico">
            </a>
        </li>

        <li class="flex-fill bd-highlight">
            <a href="{{url('notifications')}}" class="p-3 btn-mobile position-relative" title="{{ trans('general.notifications') }}">
                <span class="noti_notifications notify @if (auth()->user()->unseenNotifications()) d-block @endif">
                    {{ auth()->user()->unseenNotifications() }}
                </span>
                <img
                    src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/bell.svg') : asset('img/icons/bell-light.png') }}"
                    alt="{{ trans('admin.home') }}"
                    class="ico">
            </a>
        </li>
    </ul>
</div>

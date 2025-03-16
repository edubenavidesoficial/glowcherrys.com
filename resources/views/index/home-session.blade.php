@extends('layouts.app')

@section('content')
    <section class="section section-sm">
        <div class="container container-lg-3 pt-lg-5 pt-2">
            <div class="row">
                <div class="col-md-2">
                    @include('includes.menu-sidebar-home')
                </div>

                <div class="col-md-6 p-0 second wrap-post">
                    <div>
                        @foreach ($sliders as $index => $slider)
                            @if (strtolower($slider->estado) === 'social')
                                <div class="{{ $index == 0 ? 'active' : '' }}">
                                    <div class="text-center">
                                        <img src="{{ url($slider->image_slider) }}" class="img-center img-fluid slider-image"
                                            alt="Slide {{ $index + 1 }}">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($stories->count() || ($settings->story_status && auth()->user()->verified_id == 'yes'))
                        <div id="stories" class="storiesWrapper mb-2 p-2">
                            @if ($settings->story_status && auth()->user()->verified_id == 'yes')
                                <div class="add-story" title="{{ __('general.add_story') }}">
                                    <a class="item-add-story" href="#" data-toggle="modal" data-target="#addStory">
                                        <span class="add-story-preview">
                                            <img lazy="eager" width="100"
                                                src="{{ Helper::getFile(config('path.avatar') . auth()->user()->avatar) }}">
                                        </span>
                                        <span class="info py-3 text-center text-white bg-primary">
                                            <strong class="name" style="text-shadow: none;"><i
                                                    class="bi-plus-circle-dotted mr-1"></i>
                                                {{ __('general.add_story') }}</strong>
                                        </span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if (
                        ($settings->announcement != '' &&
                            $settings->announcement_show == 'creators' &&
                            auth()->user()->verified_id == 'yes') ||
                            ($settings->announcement != '' && $settings->announcement_show == 'all'))
                        <div class="alert alert-{{ $settings->type_announcement }} announcements display-none card-border-0"
                            role="alert">
                            <button type="button" class="close" id="closeAnnouncements">
                                <span aria-hidden="true">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                            </button>

                            <h4 class="alert-heading"><i class="bi bi-megaphone mr-2"></i>
                                {{ trans('general.announcements') }}</h4>
                            <p class="update-text">
                                {!! $settings->announcement !!}
                            </p>
                        </div><!-- end announcements -->
                    @endif

                    @if ($payPerViewsUser != 0)
                        <div class="col-md-12 d-none">
                            <ul class="list-inline">
                                <li class="list-inline-item text-uppercase h5">
                                    <a href="{{ url('/') }}"
                                        class="text-decoration-none @if (request()->is('/')) link-border @else text-muted @endif">{{ __('admin.home') }}</a>
                                </li>
                                <li class="list-inline-item text-uppercase h5">
                                    <a href="{{ url('my/purchases') }}"
                                        class="text-decoration-none @if (request()->is('my/purchases')) link-border @else text-muted @endif">{{ __('general.purchased') }}</a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @if (auth()->user()->verified_id == 'yes')
                        @include('includes.modal-add-story')

                        @include('includes.form-post')
                    @endif

                    @if ($updates->count() != 0)
                        <div class="grid-updates position-relative" id="updatesPaginator">
                            @include('includes.updates')
                        </div>
                    @else
                        <div class="grid-updates position-relative" id="updatesPaginator"></div>

                        <div class="my-5 text-center no-updates">
                            <span class="btn-block mb-3">
                                <i class="fa fa-photo-video ico-no-result"></i>
                            </span>
                            <h4 class="font-weight-light">{{ trans('general.no_posts_posted') }}</h4>
                        </div>
                    @endif
                    <div id="sugerenciasCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($sugerencias as $index => $sugerencia)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <a href="{{ url($sugerencia->username) }}" class="text-decoration-none text-reset">
                                    <div>
                                        <div class="card-im card-header bg-white border-0 d-flex justify-content-between align-items-center px-3 pt-3">
                                            <span class="text-muted fw-bold">SUGGESTIONS</span>
                                            <div class="d-flex gap-2 text-muted align-items-center">
                                                <i class="bi bi-eye-slash cursor-pointer"></i>
                                                <i class="bi bi-arrow-clockwise cursor-pointer"></i>
                                                <i class="bi bi-chevron-right cursor-pointer"></i>

                                                <!-- Botones del carrusel -->
                                                <button class="btn btn-sm btn-outline-secondary p-1 ms-2" type="button" data-bs-target="#sugerenciasCarousel" data-bs-slide="prev">
                                                    <i class="bi bi-chevron-left"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary p-1" type="button" data-bs-target="#sugerenciasCarousel" data-bs-slide="next">
                                                    <i class="bi bi-chevron-right"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="position-relative">
                                            <!-- Imagen de fondo con altura fija -->
                                            <img src="{{ $sugerencia['cover'] ? 'uploads/cover/' . $sugerencia['cover'] : 'https://picsum.photos/300/150?random=' . $index }}"
                                                 class="card-img-top rounded-3" style="height: 300px;"
                                                 alt="Fondo"
                                                 >

                                            <!-- Imagen de perfil -->
                                            <div class="position-absolute start-0 bottom-0 translate-middle-x ms-3 mb-2 image-sg">
                                                <img src="{{ $sugerencia['avatar'] ? 'uploads/avatar/' . $sugerencia['avatar'] : 'https://i.pravatar.cc/50?img=' . $index }}"
                                                     class="rounded-circle border border-white"
                                                     width="50" height="50"
                                                     alt="Perfil">
                                                <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-white rounded-circle"></span>
                                            </div>
                                        </div>

                                        <div class="card-body bg-dark text-white rounded-bottom" style="padding-left: 18%;">
                                            <h6 class="fw-bold mb-0">{{ $sugerencia['name'] }}</h6>
                                            <p class="text-muted mb-0">{{ $sugerencia['username'] }}</p>
                                        </div>
                                    </div>
                                </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div><!-- end col-md-12 -->

                <div class="col-md-4 @if ($users->count() != 0) mb-4 @endif d-lg-block d-none">
                    <div class="d-lg-block sticky-top">
                        @if ($users->count() == 0)
                            <div class="panel panel-default panel-transparent mb-4 d-lg-block d-none">
                                <div class="panel-body">
                                    <div class="media none-overflow">
                                        <div class="d-flex my-2 align-items-center">
                                            <img class="rounded-circle mr-2"
                                                src="{{ Helper::getFile(config('path.avatar') . auth()->user()->avatar) }}"
                                                width="60" height="60">

                                            <div class="d-block">
                                                <strong>{{ auth()->user()->name }}</strong>

                                                <div class="d-block">
                                                    <small class="media-heading text-muted btn-block margin-zero">
                                                        <a href="{{ url('settings/page') }}">
                                                            {{ auth()->user()->verified_id == 'yes' ? trans('general.edit_my_page') : trans('users.edit_profile') }}
                                                            <small class="pl-1"><i
                                                                    class="fa fa-long-arrow-alt-right"></i></small>
                                                        </a>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($users->count() != 0)
                            @include('includes.explore_creators')
                        @endif

                        <div class="d-lg-block d-none">
                            @include('includes.footer-tiny')
                        </div>
                    </div><!-- sticky-top -->

                </div><!-- col-md -->
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if (session('noty_error'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.error_oops') }}",
                text: "{{ trans('general.already_sent_report') }}",
                type: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('noty_success'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.thanks') }}",
                text: "{{ trans('general.reported_success') }}",
                type: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('success_verify'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.welcome') }}",
                text: "{{ trans('users.account_validated') }}",
                type: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('error_verify'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.error_oops') }}",
                text: "{{ trans('users.code_not_valid') }}",
                type: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if ($settings->story_status && $stories->count())
        <script>
            let stories = new Zuck('stories', {
                skin: 'snapssenger', // container class
                avatars: false, // shows user photo instead of last story item preview
                list: false, // displays a timeline instead of carousel
                openEffect: true, // enables effect when opening story
                cubeEffect: false, // enables the 3d cube effect when sliding story
                autoFullScreen: false, // enables fullscreen on mobile browsers
                backButton: true, // adds a back button to close the story viewer
                backNative: false, // uses window history to enable back button on browsers/android
                previousTap: true, // use 1/3 of the screen to navigate to previous item when tap the story
                localStorage: true, // set true to save "seen" position. Element must have a id to save properly.

                stories: [

                    @foreach ($stories as $story)
                        {
                            id: "{{ $story->user->username }}", // story id
                            photo: "{{ Helper::getFile(config('path.avatar') . $story->user->avatar) }}", // story photo (or user photo)
                            name: "{{ $story->user->hide_name == 'yes' ? $story->user->username : $story->user->name }}", // story name (or user name)
                            link: "{{ url($story->user->username) }}", // story link (useless on story generated by script)
                            lastUpdated: {{ $story->created_at->timestamp }}, // last updated date in unix time format

                            items: [
                                // story item example

                                @foreach ($story->media as $media)
                                    {
                                        id: "{{ $story->user->username }}-{{ $story->id }}", // item id
                                        type: "{{ $media->type }}", // photo or video
                                        length: {{ $media->type == 'photo' ? 5 : ($media->video_length ?: $settings->story_max_videos_length) }}, // photo timeout or video length in seconds - uses 3 seconds timeout for images if not set
                                        src: "{{ Helper::getFile(config('path.stories') . $media->name) }}", // photo or video src
                                        preview: "{{ $media->type == 'photo' ? Helper::getFile(config('path.stories') . $media->name) : ($media->video_poster ? Helper::getFile(config('path.stories') . $media->video_poster) : Helper::getFile(config('path.avatar') . $story->user->avatar)) }}", // optional - item thumbnail to show in the story carousel instead of the story defined image
                                        link: "", // a link to click on story
                                        linkText: '{{ $story->title }}', // link text
                                        time: {{ $media->created_at->timestamp }}, // optional a date to display with the story item. unix timestamp are converted to "time ago" format
                                        seen: false, // set true if current user was read
                                        story: "{{ $media->id }}",
                                        text: "{{ $media->text }}",
                                        color: "{{ $media->font_color }}",
                                        font: "{{ $media->font }}",
                                    },
                                @endforeach
                            ]
                        },
                    @endforeach

                ],

                callbacks: {
                    onView(storyId) {
                        getItemStoryId(storyId);
                    },

                    onEnd(storyId, callback) {
                        getItemStoryId(storyId);
                        callback(); // on end story
                    },

                    onClose(storyId, callback) {
                        getItemStoryId(storyId);
                        callback(); // on close story viewer
                    },

                    onNavigateItem(storyId, nextStoryId, callback) {
                        getItemStoryId(storyId);
                        callback(); // on navigate item of story
                    },
                },

                language: { // if you need to translate :)
                    unmute: '{{ __('general.touch_unmute') }}',
                    keyboardTip: 'Press space to see next',
                    visitLink: 'Visit link',
                    time: {
                        ago: '{{ __('general.ago') }}',
                        hour: '{{ __('general.hour') }}',
                        hours: '{{ __('general.hours') }}',
                        minute: '{{ __('general.minute') }}',
                        minutes: '{{ __('general.minutes') }}',
                        fromnow: '{{ __('general.fromnow') }}',
                        seconds: '{{ __('general.seconds') }}',
                        yesterday: '{{ __('general.yesterday') }}',
                        tomorrow: 'tomorrow',
                        days: 'days'
                    }
                }
            });

            function getItemStoryId(storyId) {
                var userActive = '{{ auth()->user()->username }}';
                if (userActive !== storyId) {
                    var itemId = $('#zuck-modal .story-viewer[data-story-id="' + storyId + '"]').find('.itemStory.active').data(
                        'id-story');
                    insertViewStory(itemId);
                }
                insertTextStory();
            }

            insertTextStory();

            function insertTextStory() {
                $('.previewText').each(function() {
                    var text = $(this).find('.items>li:first-child>a').data('text');
                    var font = $(this).find('.items>li:first-child>a').data('font');
                    var color = $(this).find('.items>li:first-child>a').data('color');
                    $(this).find('.text-story-preview').css({
                        fontFamily: font,
                        color: color
                    }).html(text);
                });
            }

            function insertViewStory(itemId) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post(URL_BASE + "/story/views/" + itemId + "");
            }

            $(document).on('click', '.profilePhoto, .info>.name', function() {
                var element = $(this);
                var username = element.parents('.story-viewer').data('story-id');
                if (username) {
                    window.location.href = URL_BASE + '/' + username;
                }
            });
        </script>
    @endif
@endsection

@section('css')
    <style>
        .slider-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 13px;
        }

        /* Asegurar que el carrusel y los elementos ocupen solo el espacio necesario */
        #sugerenciasCarousel .carousel-item {
            min-height: auto;
            /* Evita que el carrusel tenga una altura fija innecesaria */
        }

        .card {
            height: auto !important;
            /* Asegura que la tarjeta solo mida lo necesario */
        }

        .carousel-item {
    height: 38rem;
    background-color: #ffffff00;}

    .image-sg{
        padding-left: 1rem;
        padding-top: 1rem;
    }
    .card-im{
        border-radius: calc(1.25rem - 1px) calc(1.25rem - 1px) 0 0 !important;
    }

    </style>
@endsection

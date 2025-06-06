@extends('layouts.app')

@section('content')
 <!-- jumbotron -->
@if($sliders->count() > 0) <!-- Verifica si hay imágenes en el slider -->
<!-- jumbotron -->
<div id="carouselExampleIndicators" class="jumbotron homepage-slider m-0 bg-gradient carousel slide" data-ride="carousel">
  <!-- Indicadores del carrusel -->
  <ol class="carousel-indicators">
    @foreach($sliders as $index => $slider)
      <li data-target="#carouselExampleIndicators" data-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></li>
    @endforeach
  </ol>

  <!-- Elementos del carrusel -->
  <style>
    @media (max-width: 768px) {
    .carousel-caption {
        position: absolute;
        bottom: 10%; /* Ajusta la posición */
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        text-align: center;
        background: rgba(181, 8, 5, 0.14); /* Agrega fondo para mejor visibilidad */
        padding: 10px;
        border-radius: 10px;
    }
    
    .carousel-caption h1 {
        font-size: 1.5rem;
    }

    .carousel-caption p {
        font-size: 1rem;
    }

    .carousel-caption .btn {
        font-size: 0.9rem;
        padding: 8px 12px;
    }
}
</style>
  <div class="carousel-inner">
    @foreach($sliders as $index => $slider)
    @if(strtolower($slider->estado) === 'banner')
      <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" style="display: flow !important;">
        <!-- Imagen del slider como fondo -->
        <img src="{{ url($slider->image_slider) }}" class="d-block w-100 img-fluid" alt="Slide {{ $index + 1 }}">

        <!-- Contenido del slider -->
        <div class="carousel-caption d-block d-sm-block text-center">
            <div class="row">
              <div class="col-lg-5 second">
                <h1 class="display-4 pt-5 mb-3 text-white text-center-sm">{{ trans('general.welcome_title') }}</h1>
                <p class="text-white text-center-sm">{{ trans('general.subtitle_welcome') }}</p>
                <p>
                  <a href="{{ url('creators') }}" class="btn btn-lg btn-main btn-outline-light btn-w-mb px-4 mr-2" role="button">{{ trans('general.explore') }}</a>
                  <a class="btn btn-lg btn-main btn-outline-light btn-w px-4 toggleRegister btn-arrow" href="{{ $settings->registration_active == '1' ? url('signup') : url('login') }}">
                    {{ trans('general.getting_started') }}
                  </a>
                </p>
              </div>
            </div>
        </div>
      </div>
      @endif
    @endforeach
  </div>

  <!-- Controles del carrusel -->
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

@else
  <!-- Si no hay imágenes en el slider, mostrar la imagen estática -->
  <div class="jumbotron homepage m-0 bg-gradient">
    <div class="container">
      <div class="row">
        <div class="col-lg-5 second">
          <h1 class="display-4 pt-5 mb-3 text-white text-center-sm">{{ trans('general.welcome_title') }}</h1>
          <p class="text-white text-center-sm">{{ trans('general.subtitle_welcome') }}</p>
          <p>
            <a href="{{ url('creators') }}" class="btn btn-lg btn-main btn-outline-light btn-w-mb px-4 mr-2" role="button">{{ trans('general.explore') }}</a>
            <a class="btn btn-lg btn-main btn-outline-light btn-w px-4 toggleRegister btn-arrow" href="{{ $settings->registration_active == '1' ? url('signup') : url('login') }}">
              {{ trans('general.getting_started') }}
            </a>
          </p>
        </div>
        <div class="col-lg-7 first">
          <img src="{{ url('/img', $settings->home_index) }}" class="img-center img-fluid" alt="Home Image">
        </div>
      </div>
    </div>
  </div>
@endif
<!-- ./ jumbotron -->
  <div class="section py-5 py-large">
    <div class="container">
        <div class="btn-block text-center mb-5">
          <h1 class="txt-black">{{trans('general.header_box_2')}}</h1>
          <p>
            {{trans('general.desc_box_2')}}
          </p>
          </div>

          <div class="row">
            <div class="col-lg-4">
              <div class="text-center">
                <img style="width:640px!important" src="{{url('/img', $settings->img_1)}}" class="img-center img-fluid" width="120">
                <h5 class="mt-3">{{trans('general.card_1')}}</h5>
                <p class="text-muted mt-3">{{trans('general.desc_card_1')}}</p>
              </div>
          </div>

          <div class="col-lg-4">
            <div class="text-center">
              <img id="center-image" style="width:640px!important" src="{{url('/img', $settings->img_2)}}" class="img-center img-fluid" width="120">
              <h5 class="mt-3">{{trans('general.card_2')}}</h5>
              <p class="text-muted mt-3">{{trans('general.desc_card_2')}}</p>
            </div>
        </div>

        <div class="col-lg-4">
          <div class="text-center">
            <img style="width:640px!important"   src="{{url('/img', $settings->img_3)}}" class="img-center img-fluid" width="120">
            <h5 class="mt-3">{{trans('general.card_3')}}</h5>
            <p class="text-muted mt-3">{{trans('general.desc_card_3')}}</p>
          </div>
      </div>

      </div>
    </div>
  </div>

  <!-- Create profile -->
  <div class="section py-5 py-large">
    <div class="container">
      <div class="row align-items-center">
      <div class="col-12 col-lg-7 text-center mb-3">
        <img src="{{url('/img', $settings->img_4)}}" alt="User" class="img-fluid">
      </div>
      <div class="col-12 col-lg-5">
        <h1 class="m-0 card-profile txt-black">{{trans('general.header_box_3')}}</h1>
        <div class="col-lg-9 col-xl-8 p-0">
          <p class="py-4 m-0 text-muted">{{trans('general.desc_box_3')}}</p>
        </div>
        <a href="{{ $settings->registration_active == '1' ? url('signup') : url('login')}}" class="btn-arrow btn btn-lg btn-main btn-outline-primary btn-w px-4">
          {{trans('general.getting_started')}}</a>
      </div>
    </div>
    </div><!-- End Container -->
  </div><!-- End Section -->

@if ($settings->widget_creators_featured == 'on')

    @if ($users->count() != 0)
    <!-- Users -->
    <div class="section py-5 py-large">
      <div class="container">
        <div class="btn-block text-center mb-5">
          <h1 class="txt-black">{{trans('general.creators_featured')}}</h1>
          <p>
            {{trans('general.desc_creators_featured')}}
          </p>
        </div>
        <div class="row">

        @if ($usersTotal > $users->total())
          <div class="w-100 mb-3 text-center">
            <a href="{{url('creators')}}" class="float-right link-border">{{trans('general.view_all_creators')}} <small class="pl-1"><i class="bi-arrow-right"></i></small></a>
          </div>
        @endif

          <div class="owl-carousel owl-theme">
            @foreach ($users as $response)
              @include('includes.listing-creators')
          @endforeach
          </div>
        </div><!-- End Row -->
      </div><!-- End Container -->
    </div><!-- End Section -->
  @endif
@endif

  @if ($settings->show_counter == 'on')
  <!-- Counter -->
  <div class="section py-2 bg-gradient text-white">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
            <span class="mr-3 display-4"><i class="bi bi-people align-baseline"></i></span>
            <div>
              <h3 class="mb-0">{!! Helper::formatNumbersStats($usersTotal) !!}</h3>
              <h5>{{trans('general.creators')}}</h5>
            </div>
          </div>

        </div>
        <div class="col-md-4">
          <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
            <span class="mr-3 display-4"><i class="bi bi-images align-baseline"></i></span>
            <div>
              <h3 class="mb-0">{!! Helper::formatNumbersStats(Updates::count()) !!}</h3>
              <h5 class="font-weight-light">{{trans('general.content_created')}}</h5>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
            <span class="mr-3 display-4"><i class="bi bi-cash-coin align-baseline"></i></span>
            <div>
              <h3 class="mb-0">@if($settings->currency_position == 'left') {{ $settings->currency_symbol }}@endif{!! Helper::formatNumbersStats(Transactions::whereApproved('1')->sum('earning_net_user')) !!}@if($settings->currency_position == 'right'){{ $settings->currency_symbol }} @endif</h3>
              <h5 class="font-weight-light">{{trans('general.earnings_of_creators')}}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif

@if ($settings->earnings_simulator == 'on')
<!-- Earnings simulator -->
<div class="section py-5 py-large">
  <div class="container mb-4">
    <div class="btn-block text-center">
      <h1 class="txt-black">{{trans('general.earnings_simulator')}}</h1>
      <p>
        {{trans('general.earnings_simulator_subtitle')}}
      </p>
    </div>
    <div class="row">
      <div class="col-md-6">
        <label for="rangeNumberFollowers" class="w-100">
          {{ __('general.number_followers') }}
          <i class="feather icon-facebook mr-1"></i>
          <i class="feather icon-twitter mr-1"></i>
          <i class="feather icon-instagram"></i>
          <span class="float-right">
            #<span id="numberFollowers">1000</span>
          </span>
        </label>
        <input type="range" class="custom-range" value="0" min="1000" max="1000000" id="rangeNumberFollowers" onInput="$('#numberFollowers').html($(this).val())">
      </div>

      <div class="col-md-6">
        <label for="rangeMonthlySubscription" class="w-100">{{ __('general.monthly_subscription_price') }}
          <span class="float-right">
            {{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="monthlySubscription">{{ $settings->min_subscription_amount }}</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }}
        </span>
        </label>
        <input type="range" class="custom-range" value="0" onInput="$('#monthlySubscription').html($(this).val())" min="{{ $settings->min_subscription_amount }}" max="{{ $settings->max_subscription_amount }}" id="rangeMonthlySubscription">
      </div>

      <div class="col-md-12 text-center mt-4">
        <h3 class="font-weight-light">{{trans('general.earnings_simulator_subtitle_2')}}
          <span class="font-weight-bold"><span id="estimatedEarn"></span> <small>{{$settings->currency_code}}</small></span>
          {{ __('general.per_month') }}*</h3>
        <p class="mb-1">
          * {{trans('general.earnings_simulator_subtitle_3')}}
        </p>
        @if ($settings->fee_commission != 0)
          <small class="w-100 d-block">* {{trans('general.include_platform_fee', ['percentage' => $settings->fee_commission])}}</small>
        @endif
      </div>
    </div>
  </div>
</div>
@endif

    <div class="jumbotron m-0 text-white text-center bg-gradient">
      <div class="container position-relative">
        <h1>{{trans('general.head_title_bottom')}}</h1>
        <p>{{trans('general.head_title_bottom_desc')}}</p>
        <p>
          <a href="{{url('creators')}}" class="btn btn-lg btn-main btn-outline-light btn-w-mb px-4 mr-2" role="button">{{trans('general.explore')}}</a>
          <a class="btn-arrow btn btn-lg btn-main btn-outline-light btn-w px-4 toggleRegister" href="{{ $settings->registration_active == '1' ? url('signup') : url('login')}}" role="button">
          {{trans('general.getting_started')}}
        </a>
        </p>
      </div>
    </div>

@endsection

@section('javascript')

  @if ($settings->earnings_simulator == 'on')
  <script type="text/javascript">

  function decimalFormat(nStr)
  {
    @if ($settings->decimal_format == 'dot')
     var $decimalDot = '.';
     var $decimalComma = ',';
     @else
     var $decimalDot = ',';
     var $decimalComma = '.';
     @endif

     @if ($settings->currency_position == 'left')
     var currency_symbol_left = '{{$settings->currency_symbol}}';
     var currency_symbol_right = '';
     @else
     var currency_symbol_right = '{{$settings->currency_symbol}}';
     var currency_symbol_left = '';
     @endif

      nStr += '';
      var x = nStr.split('.');
      var x1 = x[0];
      var x2 = x.length > 1 ? $decimalDot + x[1] : '';
      var rgx = /(\d+)(\d{3})/;
      while (rgx.test(x1)) {
          var x1 = x1.replace(rgx, '$1' + $decimalComma + '$2');
      }
      return currency_symbol_left + x1 + x2 + currency_symbol_right;
    }

    function earnAvg() {
      var fee = {{ $settings->fee_commission }};
      @if($settings->currency_code == 'JPY')
       $decimal = 0;
      @else
       $decimal = 2;
      @endif

      var monthlySubscription = parseFloat($('#rangeMonthlySubscription').val());
      var numberFollowers = parseFloat($('#rangeNumberFollowers').val());

      var estimatedFollowers = (numberFollowers * 5 / 100)
      var followersAndPrice = (estimatedFollowers * monthlySubscription);
      var percentageAvgFollowers = (followersAndPrice * fee / 100);
      var earnAvg = followersAndPrice - percentageAvgFollowers;

      return decimalFormat(earnAvg.toFixed($decimal));
    }
   $('#estimatedEarn').html(earnAvg());

   $("#rangeNumberFollowers, #rangeMonthlySubscription").on('change', function() {

     $('#estimatedEarn').html(earnAvg());

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

@endsection

@section('css')
<style>
/* Estilos para el slider */
.homepage-slider {
  padding: 0px !important;
}

/* Altura del carrusel en pantallas normales */
.carousel-item {
  height: 38rem;
  background-color: #77777700 !important;
}

.carousel-item > img {
  height: unset !important;
}

@media (max-width: 576px) {
  .carousel-item > img {
    height: inherit !important;
  }
}
</style>
@endsection


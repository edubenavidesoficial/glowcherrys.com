<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ trans('error.error_404') }}</title>
    <link href="{{ asset('/css/core.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/styles.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ url('/img', config('settings.favicon')) }}" />
  </head>
  <body>
  		<div class="wrap-center">
  			<div class="container">
  				<div class="row">
  					<div class="col-md-12 error-page text-center parallax-fade-top" style="top: 0px; opacity: 1;">
  						<h1>404</h1>
  						<p class="mt-3 mb-5">{{ trans('error.error_404_subdescription') }}</p>
  						<a href="javascript:history.back();" class="error-link mt-5"><i class="fa fa-long-arrow-alt-left mr-2"></i> {{ trans('auth.back') }}</a><br>
							<a href="{{url('/')}}" class="error-link mt-5">{{ trans('error.go_home') }}</a>
  					</div>
  				</div>
  			</div>
  		</div>
  </body>
</html>

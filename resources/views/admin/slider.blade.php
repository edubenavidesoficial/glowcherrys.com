@extends('admin.layout')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.general_settings') }}</span>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.slider') }}</span>
    </h5>

    <div class="content">
        <div class="row">

            <div class="col-lg-12">

                @if (session('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @include('errors.errors-forms')

                <div class="card shadow-custom border-0">
                    <div class="card-body p-lg-5">

                        <form method="POST" action="{{ url('panel/admin/settings/limits') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.title_slider') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" value="{{ $settings->title }}" name="title"
                                        class="form-control">
                                </div>
                            </div><!-- end row -->
                            <div class="row mb-3">
                                <label
                                    class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.description_slider') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" value="{{ $settings->title }}" name="title"
                                        class="form-control">
                                </div>
                            </div><!-- end row -->

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.image_slider') }}</label>
                                <div class="col-lg-5 col-sm-10">
                                    <div class="d-block mb-2">
                                        <img src="{{ url('//img', $settings->logo) }}" class="bg-secondary"
                                            style="width:150px">
                                    </div>
                                    <div class="input-group mb-1">
                                        <input name="logo" type="file" class="form-control custom-file rounded-pill">
                                    </div>
                                    <small class="d-block">{{ __('general.recommended_size') }} 487x144 px (PNG)</small>
                                </div>
                            </div><!-- end row -->
                            <div class="row mb-3">
                                <label
                                    class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.link_1') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" value="" name="title"
                                        class="form-control">
                                </div>
                            </div><!-- end row -->
                            <div class="row mb-3">
                                <label
                                    class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.link_2') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" value="" name="title"
                                        class="form-control">
                                </div>
                            </div><!-- end row -->
                        </form>

                    </div><!-- card-body -->
                </div><!-- card  -->
            </div><!-- col-lg-12 -->

        </div><!-- end row -->
    </div><!-- end content -->
@endsection

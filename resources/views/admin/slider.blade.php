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

                        <form method="POST" action="{{ url('panel/admin/settings/sliders') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.title_slider') }}</label>
                                <div class="col-sm-10">
                                    <input type="text"  name="title_slider"
                                        class="form-control">
                                </div>
                            </div><!-- end row -->
                            <div class="row mb-3">
                                <label
                                    class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.description_slider') }}</label>
                                <div class="col-sm-10">
                                    <input type="text"  name="description_slider"
                                        class="form-control">
                                </div>
                            </div><!-- end row -->

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.image_slider') }}</label>
                                <div class="col-lg-5 col-sm-10">
                                    <div class="d-block mb-2">
                                        <img id="previewImage"  src="{{ url('/img', $settings->logo) }}" class="bg-secondary"
                                            style="width:150px">
                                    </div>
                                    <div class="input-group mb-1">
                                        <input id="image_slider" type="file" class="form-control custom-file rounded-pill">
                                        <input type="hidden" name="image_slider" id="base64Image">

                                    </div>
                                    <small class="d-block">{{ __('general.recommended_size') }} 487x144 px (PNG)</small>
                                </div>
                            </div><!-- end row -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.link_1') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" value="" name="link_1" class="form-control">
                                </div>
                            </div><!-- end row -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.link_2') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" value="" name="link_2" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
                                </div>
                            </div>
                        </form>

                    </div><!-- card-body -->
                </div><!-- card  -->
            </div><!-- col-lg-12 -->

        </div><!-- end row -->
    </div><!-- end content -->
@endsection
@section('javascript')
<script>
    document.getElementById('image_slider').addEventListener('change', function() {
        const file = this.files[0];
        const reader = new FileReader();

        reader.onloadend  
 = () => {
            const base64String = reader.result;
            // Actualiza un campo oculto con el valor Base64
            document.getElementById('base64Image').value = base64String;

            // Opcional: Previsualizar la imagen
            document.getElementById('previewImage').src = base64String;
        };

        reader.readAsDataURL(file);
    });
</script>
@endsection

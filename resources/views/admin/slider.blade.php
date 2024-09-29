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
                        <!-- Navegación de pestañas -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                                    type="button" role="tab" aria-controls="home"
                                    aria-selected="true">Formulary</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="table-tab" data-bs-toggle="tab" data-bs-target="#table"
                                    type="button" role="tab" aria-controls="table" aria-selected="false">Table</button>
                            </li>
                        </ul>
                        <!-- Contenido de las pestañas -->
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active my-2" id="home" role="tabpanel"
                                aria-labelledby="home-tab">
                                <form method="POST" action="{{ url('panel/admin/settings/sliders') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row mb-3">
                                        <label
                                            class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.title_slider') }}</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="title_slider" class="form-control">
                                        </div>
                                    </div><!-- end row -->
                                    <div class="row mb-3">
                                        <label
                                            class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.description_slider') }}</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="description_slider" class="form-control">
                                        </div>
                                    </div><!-- end row -->

                                    <div class="row mb-3">
                                        <label
                                            class="col-sm-2 col-form-label text-lg-end">{{ __('admin.image_slider') }}</label>
                                        <div class="col-lg-5 col-sm-10">
                                            <div class="d-block mb-2">
                                                <img id="previewImage" src="{{ url('/img', $settings->logo) }}"
                                                    class="bg-secondary" style="width:150px">
                                            </div>
                                            <div class="input-group mb-1">
                                                <input id="image_slider" type="file"
                                                    class="form-control custom-file rounded-pill">
                                                <input type="hidden" name="image_slider" id="base64Image">

                                            </div>
                                            <small class="d-block">{{ __('general.recommended_size') }} 487x144 px
                                                (PNG)</small>
                                        </div>
                                    </div><!-- end row -->
                                    <div class="row mb-3">
                                        <label
                                            class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.link_1') }}</label>
                                        <div class="col-sm-10">
                                            <input type="text" value="" name="link_1" class="form-control">
                                        </div>
                                    </div><!-- end row -->
                                    <div class="row mb-3">
                                        <label
                                            class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.link_2') }}</label>
                                        <div class="col-sm-10">
                                            <input type="text" value="" name="link_2" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-10 offset-sm-2">
                                            <button type="submit"
                                                class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade my-2" id="table" role="tabpanel" aria-labelledby="table-tab">
                                <div class="table-responsive p-0">
                                    <table class="table table-hover">
                                        <tbody>
                                            @if ($sliders->count() != 0)
                                                <tr>
                                                    <th class="active">{{ trans('admin.title_slider') }}</th>
                                                    <th class="active">{{ trans('admin.description_slider') }}</th>
                                                    <th class="active">{{ __('admin.image_slider') }}</th>
                                                    <th class="active">{{ trans('admin.link_1') }}</th>
                                                    <th class="active">{{ trans('admin.link_2') }}</th>
                                                    <th class="active">{{ trans('admin.update') }}</th>
                                                    <th class="active">{{ trans('admin.delete') }}</th>
                                                </tr>
                                                @foreach ($sliders as $slider)
                                                    <tr>
                                                        <td>{{ $slider->title_slider }}</td>
                                                        <td>{{ $slider->description_slider }}
                                                        </td>
                                                        <td>
                                                            <img src="{{ url($slider->image_slider) }}"
                                                                style="max-width: 100px; max-height: 100px;"
                                                                alt="Image">
                                                        </td>
                                                        <td>
                                                            <a href="{{ $slider->link_1 }}" target="_blank"
                                                                class="btn btn-primary rounded-pill btn-sm">
                                                                <i class="bi-link"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ $slider->link_1 }}" target="_blank"
                                                                class="btn btn-primary rounded-pill btn-sm">
                                                                <i class="bi-link"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href=""
                                                                class="btn btn-success rounded-pill btn-sm">
                                                                <i class="bi-pencil"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('panel/admin/sliders/delete', $slider->id) }}"
                                                                class="btn btn-danger rounded-pill btn-sm">
                                                                <i class="bi-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr><!-- /.TR -->
                                                @endforeach
                                            @else
                                                <h5 class="text-center p-5 text-muted fw-light m-0">
                                                    {{ trans('general.no_results_found') }}</h5>
                                            @endif
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
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

            reader.onloadend = () => {
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

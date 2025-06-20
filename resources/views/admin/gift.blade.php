@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">{{ __('admin.general_settings') }}</span>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">{{ __('admin.gifts') }}</span>
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
                            <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#form"
                                type="button" role="tab" aria-controls="form"
                                aria-selected="true">Formulary</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="table-tab" data-bs-toggle="tab" data-bs-target="#table"
                                type="button" role="tab" aria-controls="table" aria-selected="false">Table</button>
                        </li>
                    </ul>

                    <!-- Contenido de las pestañas -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active my-2" id="form" role="tabpanel"
                            aria-labelledby="form-tab">
                            <form id="giftForm" method="POST" action="{{ url('panel/admin/settings/gifts') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="gift_id" name="id" value="">

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.name') }}</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="name" name="name" class="form-control">
                                    </div>
                                </div><!-- end row -->

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.image') }}</label>
                                    <div class="col-lg-5 col-sm-10">
                                        <div class="d-block mb-2">
                                            <img id="previewImage" src="" class="bg-secondary" style="width:150px">
                                        </div>
                                        <div class="input-group mb-1">
                                            <input id="image_path" type="file" class="form-control custom-file rounded-pill">
                                            <input type="hidden" name="image" id="base64Image">
                                        </div>
                                        <small class="d-block">{{ __('general.recommended_size') }} 500x500 px</small>
                                    </div>
                                </div><!-- end row -->

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.price') }}</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" id="price" name="price" class="form-control">
                                    </div>
                                </div><!-- end row -->

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.diamonds') }}</label>
                                    <div class="col-sm-10">
                                        <input type="number" id="diamonds" name="diamonds" class="form-control">
                                    </div>
                                </div><!-- end row -->

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.status') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                            <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                                        </div>
                                    </div>
                                </div><!-- end row -->

                                <div class="row mb-3" id="createGift">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
                                    </div>
                                </div>

                                <div class="row mb-3" style="visibility: hidden;" id="updateGift">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="button" onclick="updateGift()" class="btn btn-dark mt-3 px-5">{{ __('admin.update') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade my-2" id="table" role="tabpanel" aria-labelledby="table-tab">
                            <div class="table-responsive p-0">
                                <table class="table table-hover">
                                    <tbody>
                                        @if ($gifts->count() != 0)
                                        <tr>
                                            <th>{{ trans('admin.name') }}</th>
                                            <th>{{ __('admin.image') }}</th>
                                            <th>{{ trans('admin.price') }}</th>
                                            <th>{{ trans('admin.diamonds') }}</th>
                                            <th>{{ trans('admin.status') }}</th>
                                            <th>{{ trans('admin.actions') }}</th>
                                        </tr>
                                        @foreach ($gifts as $gift)
                                        <tr>
                                            <td>{{ $gift->name }}</td>
                                            <td>
                                                <img src="{{ asset($gift->image_path) }}" style="max-width: 100px; max-height: 100px;" alt="Gift Image">
                                            </td>
                                            <td>${{ number_format($gift->price, 2) }}</td>
                                            <td>{{ $gift->diamonds }}</td>
                                            <td>
                                                @if($gift->is_active)
                                                    <span class="badge bg-success">{{ __('admin.active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('admin.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" onclick="editGift({{ $gift->id }})" class="btn btn-success rounded-pill btn-sm">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                                <a href="{{ url('/panel/admin/gifts/delete', $gift->id) }}" class="btn btn-danger rounded-pill btn-sm">
                                                    <i class="bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <h5 class="text-center p-5 text-muted fw-light m-0">
                                            {{ trans('general.no_results_found') }}
                                        </h5>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
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
    document.getElementById('image_path').addEventListener('change', function() {
        const file = this.files[0];
        const reader = new FileReader();

        reader.onloadend = () => {
            const base64String = reader.result;
            document.getElementById('base64Image').value = base64String;
            document.getElementById('previewImage').src = base64String;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });

    async function editGift(id) {
        try {
            const response = await fetch(`/panel/admin/gifts/${id}/edit`);
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }
            const gift = await response.json();

            // Rellenar el formulario
            document.getElementById('gift_id').value = gift.id;
            document.getElementById('name').value = gift.name;
            document.getElementById('price').value = gift.price;
            document.getElementById('diamonds').value = gift.diamonds;
            document.getElementById('is_active').checked = gift.is_active == 1;
            document.getElementById('previewImage').src = gift.image_path;

            // Mostrar botón de actualización y ocultar el de crear
            document.getElementById('updateGift').style.visibility = 'visible';
            document.getElementById('createGift').style.visibility = 'hidden';

            // Cambiar a la pestaña de formulario
            const tabTrigger = document.querySelector('#form-tab');
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();

        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el regalo: ' + error.message);
        }
    }

    async function updateGift() {
        const form = document.getElementById('giftForm');
        const formData = new FormData(form);
        const id = document.getElementById('gift_id').value;

        try {
            const response = await fetch(`/panel/admin/gifts/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Error: ${response.statusText}`);
            }

            const result = await response.json();
            alert(result.message || 'Regalo actualizado correctamente');
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar el regalo: ' + error.message);
        }
    }
</script>
@endsection

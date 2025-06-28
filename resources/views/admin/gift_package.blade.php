@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">{{ __('admin.general_settings') }}</span>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">{{ __('admin.gifts_package') }}</span>
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
                            <form id="giftForm" method="POST" action="{{ url('panel/admin/settings/gifts-package') }}"
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
                                    <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.quantity') }}</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="1" id="quantity" name="quantity" class="form-control">
                                    </div>
                                </div><!-- end row -->
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.price_usd') }}</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" id="price_usd" name="price_usd" class="form-control">
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
                                        @if ($gifts_packages->count() != 0)
                                        <tr>
                                            <th>{{ trans('admin.name') }}</th>
                                            <th>{{ trans('admin.quantity') }}</th>
                                            <th>{{ trans('admin.price_usd') }}</th>
                                            <th>{{ trans('admin.actions') }}</th>
                                        </tr>
                                        @foreach ($gifts_packages as $gift)
                                        <tr>
                                            <td>{{ $gift->name }}</td>
                                            <td>{{ $gift->quantity }}</td>
                                            <td>${{ number_format($gift->price_usd, 2) }}</td>
                                                            <td>
                                                <a href="#" onclick="editGift({{ $gift->id }})" class="btn btn-success rounded-pill btn-sm">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                                <a href="{{ url('/panel/admin/gifts-package/delete', $gift->id) }}" class="btn btn-danger rounded-pill btn-sm">
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
    // Variable global para almacenar el ID del gift
    let gift_id = 0;

    // Manejo de la imagen
    document.getElementById('image_path').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onloadend = () => {
            document.getElementById('base64Image').value = reader.result;
            document.getElementById('previewImage').src = reader.result;
        };
        reader.readAsDataURL(file);
    });

    // Función para editar un gift
    async function editGift(id) {
        try {
            const response = await fetch(`/panel/admin/settings/gifts-package/${id}`);
            if (!response.ok) throw new Error(`Error: ${response.status}`);

            const { modelo } = await response.json();

            // Rellenar formulario
            document.getElementById('gift_id').value = modelo.id;
            document.getElementById('name').value = modelo.name;
            document.getElementById('price_usd').value = modelo.price_usd;
            document.getElementById('quantity').value = modelo.quantity;

            // Mostrar pestaña de formulario
            new bootstrap.Tab(document.querySelector('#form-tab')).show();

            // Cambiar a modo edición
            document.getElementById('updateGift').style.visibility = 'visible';
            document.getElementById('createGift').style.visibility = 'hidden';
            gift_id = modelo.id;

        } catch (error) {
            console.error('Error al editar:', error);
            alert('Error al cargar el regalo: ' + error.message);
        }
    }

    // Función para actualizar un gift
    async function updateGift() {
        const form = document.getElementById('giftForm');
        const formData = new FormData(form);
        const updateBtn = document.querySelector('#updateGift button');

        // Deshabilitar botón durante la operación
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando...';

        try {
            // Usar POST con método spoofing para PUT
            const response = await fetch(`/panel/admin/settings/gifts-package/update/${gift_id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            if (!response.ok) throw new Error(`Error: ${response.statusText}`);

            const result = await response.json();
            alert(result.message || 'Regalo actualizado correctamente');
            window.location.reload();

        } catch (error) {
            console.error('Error al actualizar:', error);
            alert('Error al actualizar: ' + error.message);
        } finally {
            updateBtn.disabled = false;
            updateBtn.textContent = '{{ __('admin.update') }}';
        }
    }
</script>
@endsection

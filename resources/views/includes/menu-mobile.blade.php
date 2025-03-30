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
<!-- Botón de Búsqueda -->
<li class="flex-fill bd-highlight">
    <a class="p-3 btn-mobile" href="#" data-bs-toggle="modal" data-bs-target="#searchModal" title="{{ trans('general.search') }}">
        <img src="{{ auth()->user()->dark_mode == 'off' ? asset('img/icons/compass.svg') : asset('img/icons/compass-light.png') }}" 
             alt="{{ trans('admin.home') }}" 
             class="ico">
    </a>
</li>

<!-- Modal de Búsqueda -->
<!-- Modal de Búsqueda sin Backdrop -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="searchModalLabel">Search</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Formulario de búsqueda -->
          <form class="form-inline my-lg-0 position-relative" method="get" action="{{ url('creators') }}">
            <input id="searchCreatorNavbar" class="form-control search-bar @if (auth()->guest() && request()->path() == '/') border-0 @endif" type="text" required name="q" autocomplete="off" minlength="3" placeholder="{{ trans('general.find_user') }}" aria-label="Search">
            <button class="btn btn-outline-success my-sm-0 button-search e-none" type="submit"><i class="bi bi-search"></i></button>
  
            <div class="dropdown-menu dd-menu-user position-absolute" style="width: 95%; top: 48px;" id="dropdownCreators">
              <button type="button" class="d-none" id="triggerBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
              <div class="w-100 text-center display-none py-2" id="spinnerSearch">
                <span class="spinner-border spinner-border-sm align-middle text-primary"></span>
              </div>
              <div id="containerCreators"></div>
              <div id="viewAll" class="display-none mt-2">
                <a class="dropdown-item border-top py-2 text-center" href="#">{{ __('general.view_all') }}</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  

<!-- Bootstrap y Scripts Necesarios -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Estilos CSS para el Modal -->
<style>
    /* Asegurar que el modal esté al frente */
    .modal {
        z-index: 10000 !important;
        background: rgba(0, 0, 0, 0.5); /* Fondo oscuro semitransparente */
    }

    .modal-backdrop {
        z-index: 9999 !important; /* Fondo del modal */
    }

    .modal-content {
        border-radius: 20px;
        padding: 10px;
        background: #fff;
        z-index: 10001 !important; /* Contenido del modal al frente */
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-body {
        padding: 20px;
    }

    .search-bar {
        font-size: 1.2rem;
        padding: 12px;
        border-radius: 20px;
        border: 1px solid #ddd;
    }

    .button-search {
        font-size: 1.2rem;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    /* Ocultar backdrop (fondo oscuro del modal) */
.modal-backdrop {
    display: none !important;
}

/* Estilos personalizados para el modal */
.modal {
    z-index: 1050 !important; /* Asegura que el modal se muestre sobre otros elementos */
}


    @media (max-width: 768px) {
        .modal-content {
            padding: 15px;
        }

        .search-bar {
            font-size: 1rem;
            padding: 10px;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("searchModal");

    // Evento para eliminar el backdrop cuando el modal se muestra
    modal.addEventListener("shown.bs.modal", function () {
        // Eliminar cualquier modal-backdrop
        document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
    });
});
</script>
       

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

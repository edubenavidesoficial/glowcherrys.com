@include('includes.alert-payment-disabled')
<style>
.card.card-border-0.shadow-large {
  transition: all 0.4s ease;
  transform: translateY(-10px);
  box-shadow:
    0 10px 20px rgba(0,0,0,0.2),
    0 0 25px rgba(255, 0, 0, 0.5);
  border-radius: 20px;
}
</style>
<div class="progress-wrapper px-3 px-lg-0 display-none mb-3" id="progress">
    <div class="progress-info">
      <div class="progress-percentage">
        <span class="percent">0%</span>
      </div>
    </div>
    <div class="progress progress-xs">
      <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>

      <form method="POST" action="{{url('update/create')}}" enctype="multipart/form-data" id="formUpdateCreate">
        @csrf
      <div class="card mb-4 card-border-0 rounded-large shadow-large">
        <div class="blocked display-none"></div>
        <div class="card-body pb-0">

          <div class="media">
          <span class="rounded-circle mr-3">
      				<img src="{{ Helper::getFile(config('path.avatar').auth()->user()->avatar) }}" class="rounded-circle avatarUser" width="60" height="60">
      		</span>

          <div class="media-body position-relative">

            <textarea  class="form-control textareaAutoSize border-0 emojiArea mentions" name="description" id="updateDescription" data-post-length="{{$settings->update_length}}" rows="4" cols="40" placeholder="{{trans('general.write_something')}}"></textarea>
          </div>
        </div><!-- media -->

            <input class="custom-control-input d-none" id="customCheckLocked" type="checkbox" {{auth()->user()->post_locked == 'yes' ? 'checked' : ''}} name="locked" value="yes">

          <!-- Alert -->
          <div class="alert alert-danger my-3 display-none" id="errorUdpate">
           <ul class="list-unstyled m-0" id="showErrorsUdpate"></ul>
         </div><!-- Alert -->

        </div>
        <div class="card-footer bg-white border-0 pt-0 rounded-large">
          <div class="justify-content-between align-items-center">

            <div class="form-group display-none" id="price" >
              <div class="input-group mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text">{{$settings->currency_symbol}}</span>
              </div>
                  <input class="form-control isNumber" autocomplete="off" name="price" placeholder="{{trans('general.price')}}" type="text">
              </div>
            </div><!-- End form-group -->

            <div class="form-group display-none" id="titlePost" >
              <div class="input-group mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="bi-type"></i></span>
              </div>
                  <input class="form-control" autocomplete="off" name="title" maxlength="100" placeholder="{{trans('admin.title')}}" type="text">
              </div>
              <small class="form-text text-muted mb-4">
                {{ __('general.title_post_info', ['numbers' => 100]) }}
              </small>
            </div><!-- End form-group -->

            <div class="w-100">
              <span id="previewImage"></span>
              <a href="javascript:void(0)" id="removePhoto" class="text-danger p-1 px-2 display-none btn-tooltip-form" data-toggle="tooltip" data-placement="top" title="{{trans('general.delete')}}"><i class="fa fa-times-circle"></i></a>
            </div>

            <input type="file" name="photo[]" id="filePhoto" accept="image/*,video/mp4,video/x-m4v,video/quicktime,audio/mp3" multiple class="visibility-hidden filepond">

            <button type="button" class="btn btn-upload btnMultipleUpload btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.upload_media')}} ({{ trans('general.media_type_upload') }})">
              <i class="feather icon-image f-size-25"></i>
            </button>

            @if ($settings->allow_zip_files)
            <input type="file" name="zip" id="fileZip" accept="application/x-zip-compressed" class="visibility-hidden">

            <button type="button" class="btn btn-upload btn-tooltip-form p-bottom-8 e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.upload_file_zip')}}" onclick="$('#fileZip').trigger('click')">
              <i class="bi bi-file-earmark-zip f-size-25"></i>
            </button>
          @endif

            <button type="button" id="setPrice" class="btn btn-upload btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.price_post_ppv')}}">
              <i class="feather icon-tag f-size-25"></i>
            </button>

            <button type="button" id="contentLocked" class="btn btn-upload btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill {{auth()->user()->post_locked == 'yes' ? '' : 'unlock'}}" data-toggle="tooltip" data-placement="top" title="{{trans('users.locked_content')}}">
              <i class="feather icon-{{auth()->user()->post_locked == 'yes' ? '' : 'un'}}lock f-size-25"></i>
            </button>

            @if ($settings->live_streaming_status == 'on')
              <button type="button" data-toggle="tooltip" data-placement="top" title="{{trans('general.stream_live')}}" class="btn btn-upload p-bottom-8 btn-tooltip-form e-none align-bottom btnCreateLive @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill">
                  <i class="bi bi-broadcast f-size-25"></i>
              </button>
            @endif

            <button type="button" id="setTitle" class="btn btn-upload btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.title_post_block')}}">
              <i class="bi-type f-size-25"></i>
            </button>

            <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-upload p-bottom-8 btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill">
                <i class="bi-emoji-smile f-size-25"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right dropdown-emoji custom-scrollbar" aria-labelledby="dropdownEmoji">
              @include('includes.emojis')
            </div>

            <div class="d-inline-block float-right mt-3 position-relative w-100-mobile">

              <span class="d-inline-block float-right position-relative rounded-pill w-100-mobile">
                <span class="btn-blocked display-none"></span>

                <button type="submit" disabled class="btn btn-sm btn-primary rounded-pill float-right e-none w-100-mobile" data-empty="{{trans('general.empty_post')}}" data-error="{{trans('general.error')}}" data-msg-error="{{trans('general.error_internet_disconnected')}}" id="btnCreateUpdate">
                  <i></i> {{trans('general.publish')}}
                </button>
              </span>


              <div id="the-count" class="float-right my-2 mr-2">
                <small id="maximum">{{$settings->update_length}}</small>
              </div>
            </div>

          </div>
        </div><!-- card footer -->
      </div><!-- card -->
    </form>

    <div class="alert alert-primary display-none card-border-0" role="alert" id="alertPostPending">
      <button type="button" class="close mt-1" id="btnAlertPostPending">
        <span aria-hidden="true">
          <i class="bi bi-x-lg"></i>
        </span>
      </button>

        <i class="bi bi-info-circle mr-1"></i> {{ trans('general.alert_post_pending_review') }}
        <a href="{{ url('my/posts') }}" class="link-border text-white">{{ trans('general.my_posts') }}</a>
    </div><!-- end announcements -->

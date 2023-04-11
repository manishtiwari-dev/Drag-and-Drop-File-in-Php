
@php
    $current_url = url()->current();
    
    $show = 0;
@endphp


@push('style')
    <link rel="stylesheet" href="{{ asset('asset/css/dashforge.filemgr.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css">
@endpush

<x-app-layout>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0 pb-0 fw-500">
            <li class="breadcrumb-item"><a href="#" class="text-dark tx-16">Dashboard</a></li>
        </ol>
    </nav>
    <div class="card contact-content-body">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="tx-15 mg-b-0">{{ __('user-manager.media_upload') }}</h6>
            </div>
        </div>

        <div class="card-body">
            <!--Drag & Drop Start-->
            <form method="post" action="{{ url('media/store') }}" id="file" enctype="multipart/form-data" class="dropzone" id="dropzone">
                @csrf
            </form>

            <div class="row mt-3">
                <div class="col-lg-12 margin-tb">
                    <div class="text-center">
                        <a class="btn btn-success" href="" title="return to index"> upload
                        </a>
                    </div>
                </div>
            </div>
            <!--Drag & Drop End-->

        </div>
            
            <div class="row row-xs">
                @if (!empty($data))
                    @foreach ($data as $key => $img)
                        <div class=" col-sm-2  my-3 d-flex">

                            <div class="card card-file image_card">
                                <div class="dropdown-file">
                                    <a href="" class="dropdown-link" data-toggle="dropdown"><i
                                            data-feather="more-vertical"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#modalViewDetails" data-id="{{ $img->images_id }}" data-toggle="modal"
                                            class="dropdown-item details result_edit_btn"><i
                                                data-feather="info"></i>View Details</a>
                                        <a href="#media_delete" id="media_del_btn" data-id="{{ $img->images_id }}"
                                            data-toggle="modal" class="dropdown-item delete"><i
                                                data-feather="trash"></i>Delete</a>
                                    </div>
                                </div><!-- dropdown -->
                                <div class="card-file-thumb tx-danger">
                                    <img src="{{ $img->media_image }}" class="card-img-top img-fluid"
                                        alt="{{ $img->media_image }}">
                                </div>

                                <div class="card-body border-top">
                                    <h6><a href="" class="link-02">{{ $img->images_name }}.{{ $img->images_ext }}</a></h6>

                                </div>
                            </div>
                        </div><!-- col -->
                    @endforeach
                @endif
            </div>

              <!--Pagination Start-->
                {!! \App\Helper\Helper::make_pagination($total_records,$per_page,$current_page,$total_page,route('media')) !!}
              <!--Pagination End-->
        
        </div>
    </div>

    {{-- modal start --}}

    <div class="modal fade effect-scale" id="modalViewDetails" tabindex="-1" role="dialog" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body pd-20 pd-sm-30">
                    <button type="button" class="close pos-absolute t-15 r-20" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>

                    <h5 class="tx-18 tx-sm-20 mg-b-30">View Details</h5>

                    <div class="row mg-b-10">
                        <div class="col-4">IMAGE NAME:</div>

                        <div class="col-8" id="image_name"></div>

                    </div><!-- row -->

                    <div class="row mg-b-10">
                        <div class="col-4">AVAILABLE SIZE:</div>
                        <div class="col-8" id="img_size"></div>
                    </div><!-- row -->
                    <div class="row mg-b-10">
                        <div class="col-4">IMAGE URL:</div>
                        <div class="col-8" id="img_url"></div>
                    </div><!-- row -->
                    <div class="row mg-b-10">
                        <div class="col-4">IMAGE UPDATED:</div>
                        <div class="col-8" id="img_date"></div>
                    </div><!-- row -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mg-sm-l-5" data-dismiss="modal">Close</button>
                </div><!-- modal-footer -->
            </div><!-- modal-content -->
        </div><!-- modal-dialog -->
    </div><!-- modal -->

    <!--media delete modal-->
    <div class="modal fade effect-scale" id="media_delete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('user-manager.delete_media') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_task_id" name="input_field_id">
                    <p class="mg-b-0">{{ __('common.delete_confirmation') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('common.no') }}</button>
                    <button type="button" class="btn btn-primary img_delete_yes">{{ __('common.yes') }}</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end media --}}

    @push('scripts')
        <script type="text/javascript" href="{{ asset('asset/js/dashforge.filemgr.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/dropzone.js"></script>

        {{-- Drag and Drop Image Start --}}
        <script type="text/javascript">
            Dropzone.options.dropzone =
            {
                maxFilesize: 100,
                resizeQuality: 1.0,
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                addRemoveLinks: true,
                timeout: 60000,
                removedfile: function(file) 
                {
                    var name = file.upload.filename;
                    $.ajax({
                        headers: {
                                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                                },
                        type: 'POST',
                        url: '{{ url("files/destroy") }}',
                        data: {filename: name},
                        success: function (data){
                            console.log("File has been successfully removed!!");
                        },
                        error: function(e) {
                            console.log(e);
                        }});
                        var fileRef;
                        return (fileRef = file.previewElement) != null ? 
                        fileRef.parentNode.removeChild(file.previewElement) : void 0;
                },
                success: function (file, response) {
                    console.log(response);
                },
                error: function (file, response) {
                    console.log(response);
                }
            };
        </script>
        {{-- Drag and Drop Image End--}}

        <script type="text/javascript">
            $('.selectsearch').select2({
                searchInputPlaceholder: 'Search options'
            });


            // Example starter JavaScript for disabling form submissions if there are invalid fields
            (function() {
                'use strict'
                var forms = document.querySelectorAll('.needs-validation')
                // Loop over them and prevent submission
                Array.prototype.slice.call(forms)
                    .forEach(function(form) {
                        form.addEventListener('submit', function(event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }

                            form.classList.add('was-validated')
                        }, false)
                    })
            })()
        </script>

        <script>
            //modal edit title ajax
            $(document).ready(function() {

                $('.result_edit_btn').on('click', function(e) {
                    e.preventDefault();
                    var images_id = $(this).data('id');
                    console.log(images_id);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "GET",

                        url: "{{ url('media/view') }}/" + images_id,
                        data: {
                            images_id: images_id
                        },
                        dataType: "json",
                        success: function(response) {

                            $.each(response.media_list, function(key, value) {
                                console.log(value);

                                $("#image_name").html(value.images_name);
                                $("#img_size").html(value.images_size);
                                $("#img_url").html(value.media_image);
                                $("#img_date").html(value.updated_at);
                            });

                        },
                        error: function(data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                });

                $(document).on("click", "#media_del_btn", function() {
                    var task_id = $(this).data('id');

                    $('#delete_task_id').val(task_id);
                    // $('#delete_modal1').modal('show');
                });

                $(document).on('click', '.img_delete_yes', function() {
                    var images_id = $('#delete_task_id').val();

                    // $('#task_delete').modal('hide');
                    // alert(task_id);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "{{ url('media/delete') }}/" + images_id,
                        data: {
                            images_id: images_id,
                            //  _method: 'DELETE'
                        },
                        dataType: "json",
                        success: function(response) {
                            $('#media_delete').removeClass('show');
                            $('#media_delete').css('display', 'none');
                            Toaster(response.success);
                            setTimeout(function() {
                                location.reload(true);
                            }, 3000);
                            window.location.href = "{{ route('media') }}";

                        }
                    });

                });



                // $('form').submit(function(e) {
                //     e.preventDefault();
                //     var data;

                //     data = new FormData();
                //  //   data.append( 'fileInfo', input.files[0] );

                //  data.append("fileInfo",$('input[name="fileInfo"]').files[0]);

                //  //   data.append('fileInfo', $('#file')[0].files[0]);

                //   alert(data);
                //     $.ajax({
                //         url: "{{ url('media/store') }}",
                //         data: data,
                //         processData: false,
                //         type: 'POST',
                //         success: function(data) {
                //             alert(data);
                //         }
                //     });


                // });



            });
        </script>
    @endpush
</x-app-layout>

@extends('layouts.admin.master')
@section('content')
    <main id="js-page-content" role="main" class="page-content">
        <div class="subheader">
            <h1 class="subheader-title">
                <i class='subheader-icon fal fa-plus-circle'></i> Business Profile
            </h1>
        </div>
        <div class="row">
            <div class="col-lg-6 col-xl-3 order-lg-1 order-xl-1">
                <!-- profile summary -->
                <div class="card mb-g rounded-top">
                    <div class="row no-gutters row-grid">
                        <div class="col-12">
                            <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                <input type="hidden" value="{{ $business_user->id }}" id="user_id" />
                                @if ($business_user->profile_photo)
                                    <img width="100" height="100" src="{{ $business_user->profile_photo }}"
                                        class="rounded-circle shadow-2" alt="Image" />
                                @endif
                                <!-- <img src=' $business_user->profile_photo' class="rounded-circle shadow-2 img-thumbnail" alt=""> -->
                                <h5 class="mb-0 fw-700 text-center mt-3">
                                    {{ $business_user->startup_name }}
                                    <small class="text-muted mb-0">{{ $business_user->startup_location }}</small>
                                    {{ $business_user->email }}
                                </h5>

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center py-3">
                                <h5 class="mb-0 fw-700">
                                    {{ $business_user->total_following }}
                                    <small class="text-muted mb-0">Following</small>
                                </h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center py-3">
                                <h5 class="mb-0 fw-700">
                                    {{ $business_user->total_follower }}
                                    <small class="text-muted mb-0">Followers</small>
                                </h5>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 text-center">
                                @if ($business_user->is_verified == '1')
                                    <span class="badge badge-success badge-pill">Verified</span>
                                    <button class="btn btn-danger ml-4" id="unverified"><span
                                            class="fal fa-times mr-1 ml-2"></span>Unverified</button>
                                @else
                                    <button class="btn btn-primary mr-4" id="verified"> <span
                                            class="fal fa-check mr-2"></span>Verify</button>
                                    <span class="badge badge-danger badge-pill">Unverified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- photos -->
                <div id="js-lightgallery">
                    <div class="card mb-g">
                        <div class="col-12">
                            <div class="p-3">
                                <h2 class="mb-0 fs-xl">
                                    Short Videos
                                </h2>
                            </div>
                        </div>
                        <div class="col-14">
                            @if ($short > 0)
                                @foreach ($business_user->all_uploaded_videos as $all_uploaded_videos)
                                    @if ($all_uploaded_videos->type == 1)
                                        <a href="#" data-url="{{ $all_uploaded_videos->video }}" class="vid-btn"
                                            data-toggle="modal" data-target=".example-modal-fullscreen">
                                            <img src="{{ asset($all_uploaded_videos->thumbnail) }}" height="80" width="80"
                                                class="p-sm-1"></a>
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <center>
                                    <h3 class="mb-0 fs-xl"> No video uploaded </h3>
                                </center>
                            @endif


                            <!-- <a href="javascript:void(0);" class="text-center px-3 py-4 d-flex position-relative height-10 border">
                                                                <span class="position-absolute pos-top pos-left pos-right pos-bottom" style="background-image: url('img/demo/gallery/thumb/1.jpg');background-size: cover;"></span>
                                                            </a> -->
                        </div>
                    </div>
                </div>
                <!-- contacts -->
                <div class="card mb-g">
                    <div class="row row-grid no-gutters">
                        <div class="col-12">
                            <div class="p-3">
                                <h2 class="mb-0 fs-xl">
                                    Team User
                                </h2>
                            </div>
                        </div>
                        <div class="col-4">
                            @if (count($business_user->team_user) > 0)
                                @foreach ($business_user->team_user as $team_user)
                                    <a href="javascript:void(0);"
                                        class="text-center p-3 d-flex flex-column hover-highlight">
                                        <img width="100" height="100" src="{{ $team_user->user->profile_photo }}"
                                            class="rounded-circle shadow-2" alt="Image" />
                                        <span
                                            class="d-block text-truncate text-muted fs-xs mt-1">{{ $team_user->user->name }}</span>
                                        <span
                                            class="d-block text-truncate text-muted fs-xs mt-1">{{ $team_user->user->username }}</span>
                                    </a>
                                @endforeach
                            @else
                                <center>
                                    <h3 class="mb-0 fs-xl"> No Team users </h3>
                                </center>
                            @endif

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-6 order-lg-3 order-xl-2">
                <div class="card border mb-g">
                    <div class="card mb-g">
                        <div class="row row-grid no-gutters">
                            <div class="col-12">
                                <div class="p-3">
                                    <h2 class="mb-0 fs-xl">
                                        Fund
                                    </h2>
                                </div>
                            </div>
                            <div class="col-6">
                                @if ($business_user->fund != null)

                                    <div
                                        class="p-3 bg-danger-200 rounded overflow-hidden position-relative text-white mb-g ml-2 mr-2">
                                        <div class="">
                                            <h3 class="display-4 d-block l-h-n m-0 fw-500">
                                                $ {{ $business_user->fund->goal }}
                                                <small class="m-0 l-h-n">Goal</small>
                                            </h3>
                                        </div>
                                        <i class="fal fa-signal position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4 "
                                            style="font-size: 6rem;"></i>
                                    </div>

                                @endif
                            </div>
                            <div class="col-6">

                                <div
                                    class="p-3 bg-info-200 rounded overflow-hidden position-relative text-white mb-g ml-2 mr-2">
                                    <div class="">
                                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                                            $ {{ $business_user->fund->pleged }}
                                            <small class="m-0 l-h-n">Pre Pleged</small>
                                        </h3>
                                    </div>
                                    <i class="fal fa-poll-people position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4"
                                        style="font-size: 6rem;"></i>
                                </div>

                            </div>
                            <div class="col-6">

                                <div
                                    class="p-3 bg-warning-200 rounded overflow-hidden position-relative text-white mb-g ml-2 mr-2">
                                    <div class="">
                                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                                            $ {{ $business_user->fund->rise }}
                                            <small class="m-0 l-h-n">Rise</small>
                                        </h3>
                                    </div>
                                    <i class="fal fa-sunrise position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4"
                                        style="font-size: 6rem;"></i>
                                </div>

                            </div>

                            <div class="col-6">

                                <div
                                    class="p-3 bg-success-200 rounded overflow-hidden position-relative text-white mb-g ml-2 mr-2">
                                    <div class="">
                                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                                            $ {{ $business_user->fund->pledge_amount }}
                                            <small class="m-0 l-h-n">Pleged</small>
                                        </h3>
                                    </div>
                                    <i class="fal fa-sunrise position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4"
                                        style="font-size: 6rem;"></i>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-3 order-lg-2 order-xl-3">
                <div class="card mb-2">
                    <div class="card-body">
                        <a href="javascript:void(0);" class="d-flex flex-row align-items-center">
                            <div class='icon-stack display-3 flex-shrink-0'>
                                <i class="fal fa-circle icon-stack-3x opacity-100 color-primary-400"></i>
                                <i class="fal fa-user-check icon-stack-1x opacity-100 color-primary-500"></i>
                            </div>
                            <div class="ml-3">
                                <strong>
                                    Bio
                                </strong>
                                <br>
                                <a class="break-all"> {{ $business_user->bio }}</a>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card mb-g">
                    <div class="card-body">
                        <a href="javascript:void(0);" class="d-flex flex-row align-items-center">
                            <div class='icon-stack display-3 flex-shrink-0'>
                                <i class="fal fa-circle icon-stack-3x opacity-100 color-warning-400"></i>
                                <i class="fal fa-handshake icon-stack-1x opacity-100 color-warning-500"></i>
                            </div>
                            <div class="ml-3">
                                <strong>
                                    Description
                                </strong>
                                <br>
                                <a class="break-all"> {{ $business_user->description }}</a>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="card mb-g">
                    <div class="col-12">
                        <div class="p-3">
                            <h2 class="mb-0 fs-xl">
                                Long Videos
                            </h2>
                        </div>
                    </div>
                    <div class="col-14">
                        @if ($long > 0)
                            @foreach ($business_user->all_uploaded_videos as $all_uploaded_videos)
                                @if ($all_uploaded_videos->type == 2)
                                    <a href="#" data-url="{{ $all_uploaded_videos->video }}" class="vid-btn"
                                        data-toggle="modal" data-target=".example-modal-fullscreen">
                                        <img src="{{ asset($all_uploaded_videos->thumbnail) }}" height="80" width="80"
                                            class="p-sm-1"></a>
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <center>
                                <h3 class="mb-0 fs-xl"> No Video Uploaded </h3>
                            </center>
                        @endif
                        <!-- <a href="javascript:void(0);" class="text-center px-3 py-4 d-flex position-relative height-10 border">
                                                                <span class="position-absolute pos-top pos-left pos-right pos-bottom" style="background-image: url('img/demo/gallery/thumb/1.jpg');background-size: cover;"></span>
                                                            </a> -->
                    </div>
                </div>
            </div>
        </div>

        @include('admin.business.document')
        @include('admin.business.pladge')
        @include('admin.business.refund')

    </main>
    <div class="modal fade modal-fullscreen example-modal-fullscreen" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content h-100 border-0 shadow-0 bg-fusion-800">
                <button type="button"
                    class="close p-sm-2 p-md-4 text-white fs-xxl position-absolute pos-right mr-sm-2 mt-sm-1 z-index-space"
                    data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
                <div class="modal-body p-0 text-center" id="vid">
                    {{-- <video src="https://ripe-objects.s3-eu-west-2.amazonaws.com/%2Fpexels-vivaan-rupani-7351722.mp4" controls autoplay/> --}}
                </div>
            </div>
        </div>
    </div>
@endsection


@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#pledge-table').DataTable({
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.pledge.list') }}",
                    data: {
                        business_user: function() {
                            return $('#user_id').val()
                        },
                        status: function() {
                            return 2
                        }
                    }
                },
                // ajax: "{{ route('admin.pledge.list') }}",
                columns: [{
                        data: 'id',
                        name: 'Id'
                    },
                    {
                        data: 'profile_photo',
                        name: 'Profile Photo',
                        orderable: false
                    },

                    {
                        data: 'from_username',
                        name: 'from_username'
                    },
                    {
                        data: 'amount',
                        name: 'Amount'
                    },
                    {
                        data: 'status_type',
                        name: 'Status'
                    },
                    {
                        data: 'updated_at',
                        name: 'Date-Time'
                    },
                ],
                order: [0, 'desc'],
                lengthChange: true,
                dom: '<"float-left"B><"float-right"f>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                // "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                // "<'row'<'col-sm-12'tr>>" +
                // "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        titleAttr: 'Generate Excel',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        titleAttr: 'Generate CSV',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        titleAttr: 'Copy to clipboard',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm'
                    }
                ]
            });

            $('#verifiedFilter').change(function() {
                table.ajax.reload(null, false);
            });

            // Clicking the save button on the open modal for both CREATE and UPDATE
            $('#catForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                var ajaxurl = "{{ route('admin.pledge.store') }}";
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        if (data.status == 'success') {
                            $('#profile_photo').val('');
                            $('#default-example-modal').modal('hide');
                            $('#catForm').trigger("reset");
                            toastr['success']('Save successfully!');
                            table.ajax.reload(null, false);
                        } else {
                            toastr['error'](data.message);
                        }
                    },
                    error: function(response) {
                        toastr['error']('Something went wrong, Please try again!');
                        console.log('Error:', data);
                    }
                });
            });

            $(document).on("click", ".vid-btn", function() {
                var vidUrl = $(this).data('url');
                let vidTag = "<video src='" + vidUrl + "' controls autoplay height='600' width='600'/>"
                $('#vid').html('');
                $('#vid').html(vidTag);

            });


            $('#verified').click(function(id, status) {
                var user_id = $('#user_id').val();
                var ajaxurl = "{{ route('admin.business.changeStatusVerified') }}" + "?user_id=" +
                    user_id;
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            toastr['success']('Verified successfully!');
                            window.location.reload();

                        }
                        if (data.status == 'error') {
                            toastr['error'](data.message);

                        }
                        $("#verified").prop("disabled", false);
                    },
                    error: function(data) {
                        toastr['error']('Something went wrong, Please try again!');
                        console.log('Error:', data);
                        $("#unverified").prop("disabled", false);
                    }
                });
            });

            $('#unverified').click(function(id, status) {
                var user_id = $('#user_id').val();
                var ajaxurl = "{{ route('admin.business.changeStatusUnverified') }}" + "?user_id=" +
                    user_id;
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            toastr['success']('Unverified successfully!');
                            window.location.reload();
                        }
                        if (data.status == 'error') {
                            toastr['error'](data.message);
                        }
                        $("#verified").prop("disabled", false);
                    },
                    error: function(data) {
                        toastr['error']('Something went wrong, Please try again!');
                        console.log('Error:', data);
                        $("#unverified").prop("disabled", false);
                    }
                });
            });
        });


        $(document).ready(function() {
            var table = $('#refund-table').DataTable({
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.pledge.list') }}",
                    data: {
                        business_user: function() {
                            return $('#user_id').val()
                        },
                        status: function() {
                            return 4
                        }
                    }
                },
                // ajax: "{{ route('admin.pledge.list') }}",
                columns: [{
                        data: 'id',
                        name: 'Id'
                    },
                    {
                        data: 'profile_photo',
                        name: 'Profile Photo',
                        orderable: false
                    },

                    {
                        data: 'from_username',
                        name: 'from_username'
                    },
                    {
                        data: 'amount',
                        name: 'Amount'
                    },
                    {
                        data: 'updated_at',
                        name: 'Date-Time'
                    },
                ],
                order: [0, 'desc'],
                lengthChange: true,
                dom: '<"float-left"B><"float-right"f>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                // "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                // "<'row'<'col-sm-12'tr>>" +
                // "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        titleAttr: 'Generate Excel',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        titleAttr: 'Generate CSV',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        titleAttr: 'Copy to clipboard',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm'
                    }
                ]
            });
        });
        $(document).ready(function() {
            var user_id = $('#user_id').val();
            var ajaxurl = "{{ route('admin.document.list') }}" + "?user_id=" +
                user_id;
            var table = $('#document-table').DataTable({
                responsive: true,
                serverSide: true,

                ajax: {
                    url: ajaxurl,
                    data: {
                        type: function() {
                            return $('#typeFilter').val()
                        },
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'Id'
                    },
                    {
                        data: 'document_type',
                        name: 'Document Type',
                        orderable: false
                    },
                    {
                        data: 'document',
                        name: 'Document',
                        orderable: false
                    },
                ],
                order: [0, 'desc'],
                lengthChange: true,
                dom: '<"float-left"B><"float-right"f>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                // "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                // "<'row'<'col-sm-12'tr>>" +
                // "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        titleAttr: 'Generate Excel',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        titleAttr: 'Generate CSV',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        titleAttr: 'Copy to clipboard',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm'
                    }
                ]
            });
            $('#btn-add').click(function() {
                $('#catForm').trigger("reset");
            });

            $('#verifiedFilter').change(function() {
                table.ajax.reload(null, false);
            });
            $('#typeFilter').change(function() {
                table.ajax.reload(null, false);
            });

            // Clicking the save button on the open modal for both CREATE and UPDATE
            $('#catForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                var ajaxurl = "{{ route('admin.document.store') }}";
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        if (data.status == 'success') {
                            $('#document').val('');
                            $('#default-example-modal').modal('hide');
                            $('#catForm').trigger("reset");
                            toastr['success']('Save successfully!');
                            table.ajax.reload(null, false);
                        } else {
                            toastr['error'](data.message);
                        }
                    },
                    error: function(response) {
                        toastr['error']('Something went wrong, Please try again!');
                        console.log('Error:', data);
                    }
                });
            });



            $(document).on("click", ".edit-cat", function() {
                var ajaxurl = $(this).data('url');
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $('#catForm').trigger("reset");
                        $('#cat_id').val(data.data.id);
                        $('#name').val(data.data.name);
                        $('#email').val(data.data.email);

                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });

            });
        });
    </script>
@endsection

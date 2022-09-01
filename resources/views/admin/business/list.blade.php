@extends('layouts.admin.master') 
@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-user-tie'></i> Business User <span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
        {{-- <small>
            Insert page description or punch line
        </small> --}}
    </h1>
</div>
<!-- Your main content goes below here: -->
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                Business User <span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                 <!-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add Wholeseller</button> -->
                <div class="panel-content" >
                    <div id="categoryData">
                        <table id="business-table" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th>Id </th>
                                    <th>Profile</th>
                                    <th>Startup Name </th>
                                    <th>Email </th>
                                    <th><select id="verifiedFilter" class="form-control">
                                        <option value="">All</option>
                                        <option value="1">Verified</option>
                                        <option value="2">Unverified</option>
                                      </select>
                                    </th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script type="text/javascript">
    $(document).ready(function(){
        var table =  $('#business-table').DataTable(
                {
                    responsive: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.business.list') }}",
                        data: {
                            is_verified: function() { return $('#verifiedFilter').val() },
                        }
                    },
                    // ajax: "{{ route('admin.business.list') }}",
                    columns: [
                        {data: 'id', name: 'Id'},
                        {data: 'profile_photo', name: 'Profile Photo',orderable: false},
                        {data: 'startup_name', name: 'Startup Name'},
                        {data: 'email', name: 'Email'},
                        {data: 'is_verified', name: 'is_verified', orderable: false, searchable: false},
                        {data: 'action', name: 'Action', orderable: false, searchable: false},
                    ],
                    order: [0, 'desc'],
                    lengthChange: true,
                    dom: '<"float-left"B><"float-right"f>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                        // "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                        // "<'row'<'col-sm-12'tr>>" +
                        // "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        {
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
                $('#btn-add').click(function () {
            $('#catForm').trigger("reset");
        });

        $('#verifiedFilter').change(function(){
            table.ajax.reload(null,false);
        });

        $(document).on("click", ".edit-cat" , function () {
            var ajaxurl = $(this).data('url');
            $.ajax({
                type: "GET",
                url: ajaxurl,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (data) {
                    $('#catForm').trigger("reset");
                    $('#cat_id').val(data.data.id);
                    $('#name').val(data.data.name);
                    $('#email').val(data.data.email);
        
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            
        });

        // $(document).on('click', '.active', function(){
        //     var ajaxurl = $(this).data('url');
        //     $.ajax({
        //         type: "GET",
        //         url: ajaxurl,
        //         dataType: 'json',
        //         headers: {
        //             'X-CSRF-TOKEN': "{{csrf_token()}}"
        //         },
        //         success: function (data) {
        //             if(data.status == 'success')
        //             {
        //                 toastr['success']('Status is changed successfully!!');
        //                 table.ajax.reload( null, false);
        //             }
        //         },
        //         error: function (data) {
        //             console.log('Error:', data);
        //         }
        //     });
        // });
       
    });
</script>
@endsection


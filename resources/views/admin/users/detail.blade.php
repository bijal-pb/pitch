@extends('layouts.admin.master') 
@section('content')
<ol class="breadcrumb page-breadcrumb">
    {{-- <li class="breadcrumb-item"><a href="javascript:void(0);">SmartAdmin</a></li>
    <li class="breadcrumb-item">Datatables</li>
    <li class="breadcrumb-item active">Basic</li> --}}
    <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
</ol>
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-users'></i>Pledge Users <span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
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
                   Pledge Users Detail<span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                <div class="panel-content" >
                    <div id="categoryData">
                        <table id="pledge-table" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th>Id </th>
                                    <th>Profile</th>
                                    <th>Pledge To</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Pledge Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" value="{{ $user->id }}" id="user_id">
    </div>
    @include('admin.users.create')
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
                            pledge_user: function() { return $('#user_id').val() },
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
                    data: 'to_username',
                    name: 'to_username'
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
                // {data: 'is_verified', name: 'is_verified', orderable: false, searchable: false},
                //  {data: 'action', name: 'Action', orderable: false, searchable: false},
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
</script>
@endsection

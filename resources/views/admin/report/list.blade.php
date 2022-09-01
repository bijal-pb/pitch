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
            <i class='subheader-icon fal fa-file-chart-line'></i>Report<span class='fw-300'></span> <sup
                class='badge badge-primary fw-500'></sup>
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
                        Report<span class="fw-300"><i></i></span>
                    </h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10"
                            data-original-title="Collapse"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                            data-offset="0,10" data-original-title="Fullscreen"></button>
                        {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                    </div>
                </div>
                <div class="panel-container show">
                        <div class="col-6 col-lg-6 input-group float-left mt-2 mr-1 form-group">
                            <label class="form-label" for="single-default">
                                Select Business
                            </label>
                            <select class="select2 form-control" id="businessId">
                                @foreach ($users as $c)
                                    <option value="{{ $c->id }}"> {{ $c->startup_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="form-group">
                            <label class="col-form-label col-3 col-lg-3 form-label text-lg-right"></label>
                            <div class="col-4 col-lg-4 input-group float-right mt-2 mr-1">
                                <input type="text" class="form-control" id="datepicker-1" placeholder="Select date"
                                    value="">
                                <div class="input-group-append">
                                    <span class="input-group-text fs-xl">
                                        <i class="fal fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            {{-- <div class="col-2 mt-5"> 
                                <button class="btn btn-success" onclick="generate()">Generate</button>
                            </div>
                            <div class="col-2 mt-5"> 
                                <button class="btn btn-success" onclick="printReport()">Print</button>
                            </div> --}}
                            <div class="col-4 mt-5"> 
                                <button class="btn btn-success" id="exportPdf">Export pdf</button>
                            </div>
                        </div>
                        {{-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add User</button> --}}
                        <div class="panel-content">
                           
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="container" id="printdivcontent">
                <div data-size="A4">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="d-flex align-items-center mb-5">
                                <h2 class="keep-print-font fw-500 mb-0 text-primary flex-1 position-relative">
                                    <span id="s_name">Startup Name</span>
                                    <small class="text-muted mb-0 fs-xs">
                                    <span id="s_location">Startup Location</span>
                                    </small>
                                </h2>
                            </div>
                            <table class="table mt-5">
                                <thead>
                                    <tr>
                                        <th class="fw-300 fw-500 color-primary-600 keep-print-font">Pledge Goal</th>
                                        <th class="fw-300 fw-500 color-primary-600 keep-print-font">Pledged Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>$<span id="goal"></span></td>
                                        <td>$<span id="total_pledged"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 class="fw-300 fw-500 color-primary-600 keep-print-font pt-4 l-h-n m-0">
                                Pledge History 
                            </h3>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table mt-5">
                                    <thead>
                                        <tr>
                                            <th class="text-center border-top-0 table-scale-border-bottom fw-700">#</th>
                                            <th class="border-top-0 table-scale-border-bottom fw-700">Pledge By</th>
                                            <th class="border-top-0 table-scale-border-bottom fw-700">Pledge Date</th>
                                            <th class="text-right border-top-0 table-scale-border-bottom fw-700">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rData">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 ml-sm-auto">
                            <table class="table table-clean">
                                <tbody>
                                    <tr class="table-scale-border-top border-left-0 border-right-0 border-bottom-0">
                                        <td class="text-left keep-print-font">
                                            <h4 class="m-0 fw-700 h2 keep-print-font color-primary-700">Total</h4>
                                        </td>
                                        <td class="text-right keep-print-font">
                                            <h4 class="m-0 fw-700 h2 keep-print-font">$<span id="total_amount"></span></h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="py-5 text-primary">
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div id="editor"></div>
            <input type="text" id="start-date" value="" hidden>
            <input type="text" id="end-date" value="" hidden> --}}
        </div>
    @endsection

    @section('page_js')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.debug.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script> --}}



    <script type="text/javascript">

        
    
        $(document).ready(function() {


        //     var doc = new jsPDF('p', 'pt', 'a4');
        //     var specialElementHandlers = {
        //         '#editor': function (element, renderer) {
        //             return true;
        //         }
        //     };

            $('#exportPdf').click(function () {
                $('#exportPdf').html('<span class="spinner-border spinner-border-sm"></span> Export Pdf');
                $('#exportPdf').prop('disabled', true);
                var data = { business_id: $('#businessId').val() }
                var ajaxurl = "{{ route('admin.generate.report') }}";
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        var blob = new Blob([response]);
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "Sample.pdf";
                        link.click();
                        $('#exportPdf').html('Export Pdf')
                        $('#exportPdf').prop('disabled', false);
                    },
                    error: function(blob) {
                        $('#exportPdf').html('Export Pdf')
                        $('#exportPdf').prop('disabled', false);
                        toastr['error']('Something went wrong, Please try again!');
                        console.log('Error:', blob);
                    }
                });
            });

        //     $('.select2').select2();
        //     // $('#datepicker-1, #datepicker-modal-2').daterangepicker({
        //     //     opens: 'left',
        //     //     locale: {
        //     //         format: 'DD-MM-YYYY'
        //     //     },
        //     // }, function(start, end, label) {
        //     //     $('#start-date').val(start.format('YYYY-MM-DD'));
        //     //     $('#end-date').val(end.format('YYYY-MM-DD'));
        //     // });

        });
        // function generate(){
        //     // alert($('#businessId').val())
        //     var formData = {
        //         business_id: $('#businessId').val(),
        //     };
        //     var ajaxurl = "{{ route('admin.report.get') }}";
        //     $.ajax({
        //         type: "POST",
        //         url: ajaxurl,
        //         data: formData,
        //         dataType: 'json',
        //         headers: {
        //             'X-CSRF-TOKEN': "{{csrf_token()}}"
        //         },
        //         success: function (data) {
        //             if(data.status == 'success')
        //             {
        //                 $('#s_name').html(data.business.startup_name);
        //                 $('#s_location').html(data.business.startup_location);
        //                 var rowData = "";
        //                 var pledges = data.pledges;
        //                 pledges.forEach((element, index) => {
        //                     rowData = rowData + "<tr>" +
        //                                     "<td class='text-center fw-700'>"+(index+1)+"</td>"+
        //                                     "<td class='text-left strong'>"+element.pledge_by.name+"</td>"+
        //                                     "<td class='text-left'>"+moment(element.updated_at).format('DD/MM/YYYY')+"</td>"+
        //                                     "<td class='text-right'>"+(element.amount).toFixed(2)+"</td>"+
        //                                 "</tr>";
        //                 });
        //                 $('#rData').html(rowData);
        //                 if(data.total.pledging_amount != null){
        //                     $('#total_amount').html((data.total.pledging_amount).toFixed(2));
        //                 }else{
        //                     $('#total_amount').html(0);
        //                 }
        //                 $('#goal').html((data.goal).toFixed(2));
        //                 $('#total_pledged').html((data.total.pledging_amount).toFixed(2));
        //             }
        //         },
        //         error: function (data) {
        //             toastr['error']('Something went wrong, Please try again!');
        //             console.log('Error:', data);
        //         }
        //     });
        // }
        // function printReport() {  
        //     var divContents = document.getElementById("printdivcontent").innerHTML;  
        //     var printWindow = window.open('', '', 'height=100%,width=auto');  
        //     printWindow.document.write('<html><head><title>Pledge Report</title>');  
        //     printWindow.document.write('<link id="vendorsbundle" rel="stylesheet" media="screen, print" href="{{ URL::asset('css/app.css') }}">');
        //     printWindow.document.write('<link id="vendorsbundle" rel="stylesheet" media="screen, print" href="{{ URL::asset('admin_assets/css/vendors.bundle.css') }}">');
        //     printWindow.document.write('<link id="appbundle" rel="stylesheet" media="screen, print" href="{{ URL::asset('admin_assets/css/app.bundle.css')}}">');
        //     printWindow.document.write('</head><body >');  
        //     printWindow.document.write(divContents);  
        //     printWindow.document.write('</body></html>');  
        //     printWindow.focus();
        //     setTimeout(function(){ printWindow.print(); }, 1000);
        // }  
    </script>
    @endsection

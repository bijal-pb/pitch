@extends('layouts.admin.master') 
@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-bell'></i> Notifications <span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
        {{-- <small>
            Insert page description or punch line
        </small> --}}
    </h1>
</div>
<!-- Your main content goes below here: -->
<div class="row">
    
    <div class="col-xl-12">
        <!-- <div class="alert alert-warning" role="alert">
            Don't do any changes if you're not sure without help of technical Team / Developer, This changes are not revertable and direct apply on live application and might be shout down or create errors to live Users.
        </div> -->
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Notifications <span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
        <div class="panel-container show">
            <div class="panel-content" >
                <form id="profileForm">
                     <div class="form-group">
                        <label class="form-label" for="device_type">Device</label>
                        <select id="device_type" name="device_type" class="form-control">
                            <option value='all' selected>All</option>
                            <option value='android'>Android</option>
                            <option value='ios'>IOS</option>
                        </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" id="title" name="title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="message">Message</label>
                            <textarea type="text" id="message" name="message" class="form-control"></textarea>
                        </div>
                    </form>
                        <div class="mt-5 form-group text-center">
                            <button id="save" class="btn btn-primary">Send</button>
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
        $("#save").click(function (e) {
            var formData = {
                device_type: $('#device_type').val(),
                title: $('#title').val(),
                message: $('#message').val(),
            };
            var ajaxurl = "{{ route('admin.notification.send') }}";
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (data) {
                    if(data.status == 'success')
                    {
                        toastr['success']('Notification sent successfully!');
                        $('#profileForm').trigger("reset");  
                    }
                    if(data.status == 'error')
                    {
                        toastr['error'](data.message);
                    }
                    $("#save").prop("disabled", false);
                },
                error: function (data) {
                    toastr['error']('Something went wrong, Please try again!');
                    console.log('Error:', data);
                    $("#save").prop("disabled", false);
                }
            });
            
        });
    });
</script>
@endsection

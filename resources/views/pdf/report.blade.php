<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - #123</title>

    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .invoice table {
            margin: 15px;
        }

        .invoice h3 {
            margin-left: 15px;
        }

        .information {
            background-color:#7a59ad;
            color: #FFF;
        }

        .information .logo {
            margin: 5px;
            border-radius: 50% !important;
        }

        .information table {
            padding: 10px;
        }

        .b-color {
            border-style:solid;
            border-color: #7a59ad;
            height: 40px;
            text-align: center;
        }
    </style>

</head>
<body>

<div class="information">
    <table width="100%">
        <tr>
            <td align="left" style="width: 40%;">
                <h2>{{ $startup_name }}</h2>
                <pre>{{ $startup_location }},
{{ $email }}
{{ $website }}</pre>


            </td>
            <td align="right" style="width: 40%;">
                @if($logo != null)
                <img src="{{ $logo }}" alt="Logo" width="50" height="50" class="logo"/>
                @endif
            </td>
        </tr>

    </table>
</div>

<br/>
<div class="fund">
<table width="100%">
    <tr>
        <td class="b-color">Goal<br> ${{ number_format($fund->goal,2) }}</td>
        <td class="b-color">Pre Pledged <br> ${{ number_format($fund->pleged,2) }}</td>
        <td class="b-color">Rise <br> ${{ number_format($fund->rise,2) }}</td>
        <td class="b-color">Pldged <br> ${{ number_format($fund->pledge_amount,2) }}</td>
    </tr>
</table>
</div>

<br/>



<div class="invoice">
    <h3>Pledges</h3>
    <table width="95%">
        <thead>
            <tr style="background: #7a59ad; height: 20px; color:white;">
                <th>Id </th>
                <th>Name</th>
                <th>Date</th>
                <th>Refund</th>
                <th>Pledge</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pledges as $pledge)
                @if($pledge->status == 2)
                    <tr>
                        <td style="text-align: center">{{ $pledge->id }}</td>
                        <td style="text-align: center">{{ $pledge->pledge_by->name }}</td>
                        <td style="text-align: center">{{ date('d-m-Y', strtotime($pledge->updated_at)) }}</td>
                        <td style="text-align: right"></td>
                        <td style="text-align: right">${{ number_format($pledge->amount,2) }}</td>
                        <td style="text-align: right">${{ number_format($pledge->amount,2) }}</td>
                    </tr>
                @endif
                @if($pledge->status == 4)
                    <tr>
                        <td style="text-align: center">{{ $pledge->id }}</td>
                        <td style="text-align: center">{{ $pledge->pledge_by->name }}</td>
                        <td style="text-align: center">{{ date('d-m-Y', strtotime($pledge->updated_at)) }}</td>
                        <td style="text-align: right">${{ number_format($pledge->amount,2) }}</td>
                        <td style="text-align: right"></td>
                        <td style="text-align: right">- ${{ number_format($pledge->amount,2) }}</td>
                    </tr>
                @endif
            @endforeach
            
        </tbody>

        <tfoot>
        <tr style="background: #7a59ad; height: 20px; color:white;">
            <td colspan="3" style="text-align: center">Total</td>
            <td align="right">${{ number_format($total_refund, 2) }}</td>
            <td align="right">${{ number_format($total_pledge,2) }}</td>
            <td align="right">${{ number_format($fund->pledge_amount,2) }}</td>
        </tr>
        </tfoot>
    </table>
</div>

{{-- <div class="information" style="bottom: 0;">
    <table width="100%">
        <tr>
            <td align="left" style="width: 50%;">
                &copy; {{ date('Y') }} {{ config('app.url') }} - All rights reserved.
            </td>
            <td align="right" style="width: 50%;">
                Company Slogan
            </td>
        </tr>

    </table>
</div> --}}
</body>
</html>
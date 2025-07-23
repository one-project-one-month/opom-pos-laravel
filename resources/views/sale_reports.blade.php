<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Reports</title>
    <style>
        h1{
            text-align:center;
            font-family:monospace;
            font-size:25px;
        }
        table{
            border:1px solid black;
            width:100%;
            border-collapse: collapse;
            font-family:monospace;
        }
        table th{
            background-color:black;
            color:white;
            padding: 10px 0px;
            font-size:17px;
        }
        table td{
            font-size:15px;
            padding:7px 0px;
            text-align:center;
            border:1px solid black;
        }
    </style>
</head>
<body>
    <h1>{{ucfirst($time)}} {{ucfirst($choice)}} Sale Products list</h1>
    <table>
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        @php
            $no = 0
        @endphp
        @foreach($OrderItems as $item)
            <tr>
                <td>{{++$no}}</td>
                <td>{{$item->product->name}}</td>
                <td>{{$item->total_quantity}}</td>
                <td>{{$item->price}}</td>
                <td>{{$item->total}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>

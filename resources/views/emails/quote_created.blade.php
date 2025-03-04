<!DOCTYPE html>
<html>
<head>
    <title>Quote Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            margin: 10px 0;
            color: #555;
        }
        .content a {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }
        .footer p {
            margin: 0;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quote Created</h1>
        </div>
        <div class="content">
            <p>Dear {{ $quote->customer->user->name }},</p>
            <p>Your quote has been created successfully. Here are the details:</p>
            <p><strong>Quote ID:</strong> {{ $quote->id }}</p>
            <p><strong>Vehicle Type:</strong> {{ $quote->vehicleType->name }}</p>
            <p><strong>Pickup Locations:</strong> {{ implode(', ', $quote->pickup_locations) }}</p>
            <p><strong>Dropoff Locations:</strong> {{ implode(', ', $quote->dropoff_locations) }}</p>
            {{-- <p><strong>Estimated Distance:</strong> {{ $quote->estimated_distance }} km</p> --}}
            <p><strong>Estimated Fare:</strong> £{{ $quote->estimated_fare }}</p>
            <p><strong>Status:</strong> {{ ucfirst($quote->status) }}</p>
            <p>Please click the link below to view and pay for your quote:</p>
            <a href="{{ $paymentLink }}">Pay Now</a>
        </div>
        <div class="footer">
            <p>Thank you for choosing our service!</p>
            <p>Best regards,</p>
            {{-- <p>Your Company Name</p> --}}
        </div>
    </div>
</body>
</html>
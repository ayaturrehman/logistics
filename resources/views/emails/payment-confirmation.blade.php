<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .content {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .details {
            margin: 20px 0;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
        }

        .details th,
        .details td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Payment Confirmation</h1>
    </div>

    <div class="content">
        <p>Dear {{ $quote->customer->user->name }},</p>

        <p>Thank you for your payment. This email confirms that your payment for Quote #{{ $quote->id }} has been
            successfully processed.</p>

        <div class="details">
            <h2>Payment Details</h2>
            <table>
                <tr>
                    <th>Quote ID:</th>
                    <td>#{{ $quote->id }}</td>
                </tr>
                <tr>
                    <th>Amount Paid:</th>
                    <td>${{ number_format($quote->amount_paid, 2) }}</td>
                </tr>
                <tr>
                    <th>Payment Date:</th>
                    {{-- <td>{{ $quote->payment_details['payment_date'] }}</td> --}}
                </tr>
                <tr>
                    <th>Payment Method:</th>
                    <td>{{ ucfirst($quote->payment_method) }}</td>
                </tr>
                <tr>
                    <th>Payment Status:</th>
                    <td>{{ ucfirst($quote->payment_status) }}</td>
                </tr>
            </table>

            <h2>Transport Details</h2>
            <table>
                <tr>
                    <th>Pickup Location:</th>
                    <td>{{ $quote->pickup_location }}</td>
                </tr>
                <tr>
                    <th>Delivery Location:</th>
                    <td>{{ $quote->delivery_location }}</td>
                </tr>
                <tr>
                    <th>Pickup Date:</th>
                    <td>{{ $quote->pickup_date }}</td>
                </tr>
                <tr>
                    <th>Delivery Date:</th>
                    <td>{{ $quote->delivery_date }}</td>
                </tr>
                <tr>
                    <th>Transport Type:</th>
                    <td>{{ $quote->transport_type }}</td>
                </tr>
                <tr>
                    <th>Vehicle Type:</th>
                    <td>{{ $quote->vehicle_type }}</td>
                </tr>
            </table>

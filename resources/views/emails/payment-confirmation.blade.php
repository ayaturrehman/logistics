<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Authorization - A2B Logistiks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Header */
        .header {
            background-color: #28a745;
            padding: 25px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 16px;
        }
        
        /* Content */
        .content {
            padding: 25px;
        }
        
        /* Success icon */
        .success-icon {
            text-align: center;
            margin: 20px 0;
        }
        
        /* Payment card */
        .payment-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .card-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .card-title {
            color: #28a745;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }
        
        /* Detail rows */
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .detail-label {
            color: #555;
            font-weight: 500;
        }
        .detail-value {
            color: #212529;
            font-weight: 600;
            text-align: right;
        }
        
        /* Transport section */
        .transport-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #1a3c8a;
        }
        .section-title {
            color: #1a3c8a;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        /* Location */
        .location {
            margin-bottom: 15px;
        }
        .location-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #1a3c8a;
        }
        
        /* Next steps */
        .next-steps {
            background-color: #fff3cd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .steps-list {
            margin: 0;
            padding-left: 20px;
        }
        .steps-list li {
            margin-bottom: 8px;
        }
        
        /* Footer */
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
        }
        
        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            background-color: #17a2b8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Your Payment is Ready</h1>
            <p>Quote #{{ $quote->id }}</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $quote->customer->user->name }},</p>
            
            <p>Thank you for choosing A2B Logistiks. <strong>Important: Your card will only be charged when we collect your vehicle.</strong></p>
            
            <div class="success-icon">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C6.477 22 2 17.523 2 12C2 6.477 6.477 2 12 2C17.523 2 22 6.477 22 12C22 17.523 17.523 22 12 22ZM11.003 16L18.073 8.929L16.659 7.515L11.003 13.172L8.174 10.343L6.76 11.757L11.003 16Z" fill="#28a745"/>
                </svg>
            </div>
            
            <div class="payment-card">
                <div class="card-header">
                    <h2 class="card-title">Payment Details</h2>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">£{{ number_format($quote->estimated_fare, 2) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ now()->format('d M Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge">Ready</span>
                    </span>
                </div>
            </div>
            
            <div class="transport-section">
                <h2 class="section-title">Transport Summary</h2>
                
                <div class="location">
                    <div class="location-title">From:</div>
                    <div>{{ $quote->pickup_locations['text'] ?? 'N/A' }}</div>
                </div>
                
                <div class="location">
                    <div class="location-title">To:</div>
                    <div>{{ $quote->dropoff_locations['text'] ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Vehicle:</span>
                    <span class="detail-value">{{ $quote->vehicle_make }} {{ $quote->vehicle_model }}</span>
                </div>
                
                @if($quote->number_plate)
                <div class="detail-row">
                    <span class="detail-label">Registration:</span>
                    <span class="detail-value">{{ $quote->number_plate }}</span>
                </div>
                @endif
            </div>
            
            <div class="next-steps">
                <h2 class="section-title">What Happens Next?</h2>
                <ul class="steps-list">
                    <li>We'll schedule your vehicle collection based on your availability</li>
                    <li>Our team will contact you to confirm pickup details</li>
                    <li>Your payment will only be processed when we collect your vehicle</li>
                </ul>
            </div>
            
            <p>Need help? Contact us at <a href="mailto:info@a2blogistiks.uk">info@a2blogistiks.uk</a> or call <a href="tel:+447398229432">07398 229432</a>.</p>
            
            <p>Thank you for choosing A2B Logistiks!</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} A2B Logistiks UK. All rights reserved.</p>
            <p>This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
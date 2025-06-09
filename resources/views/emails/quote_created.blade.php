<!DOCTYPE html>
<html>
<head>
    <title>A2B Logistiks - Quote Confirmation</title>
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
            max-width: 650px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        /* Header styles */
        .header {
            background-color: #1a3c8a;
            background-image: linear-gradient(135deg, #1a3c8a 0%, #2a4caa 100%);
            padding: 35px 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .header:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('https://a2blogistiks.uk/images/pattern.png');
            opacity: 0.1;
            pointer-events: none;
        }
        .logo {
            max-width: 160px;
            margin-bottom: 18px;
        }
        .header h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 17px;
        }
        
        /* Content styles */
        .content {
            padding: 35px;
        }
        .greeting {
            font-size: 20px;
            margin-bottom: 22px;
            color: #1a3c8a;
            font-weight: 500;
        }
        
        /* Quote details card */
        .quote-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border-left: 5px solid #1a3c8a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .section-title {
            color: #1a3c8a;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(26, 60, 138, 0.1);
            font-weight: 600;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            color: #495057;
            font-weight: 500;
            padding-right: 15px; /* Added spacing between label and value */
            flex: 1; /* Takes up available space */
        }
        .detail-value {
            color: #212529;
            font-weight: 600;
            text-align: right;
            flex: 1; /* Takes up available space */
        }
        
        /* Location details */
        .location-details {
            margin: 30px 0;
        }
        .location-card {
            background-color: #f8f9fa;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .location-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            color: #1a3c8a;
            margin-bottom: 12px;
            font-size: 17px;
        }
        .location-title svg {
            margin-right: 10px;
            flex-shrink: 0;
        }
        .location-address {
            margin-left: 28px;
            line-height: 1.5;
            color: #495057;
        }
        
        /* Contact cards */
        .contact-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border-left: 5px solid #218838;
        }
        
        /* CTA Button */
        .cta-container {
            text-align: center;
            margin: 35px 0;
        }
        .cta-button {
            display: inline-block;
            padding: 16px 40px;
            background-color: #28a745;
            background-image: linear-gradient(135deg, #28a745 0%, #1e8a3b 100%);
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 50px;
            font-size: 17px;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }
        .cta-button:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
        }
        
        /* Price highlight */
        .price-highlight {
            font-size: 26px;
            color: #28a745;
            font-weight: 700;
        }
        
        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
        }
        .status-pending {
            background-color: #ffc107;
        }
        .status-authorized {
            background-color: #17a2b8;
        }
        .status-paid {
            background-color: #28a745;
        }
        
        /* Footer */
        .footer {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
        }
        .social-links {
            margin: 18px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6c757d;
            text-decoration: none;
        }
        
        /* Mobile responsiveness */
        @media screen and (max-width: 480px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .header {
                padding: 25px 20px;
            }
            .content {
                padding: 25px 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
        
        /* Vehicle details section */
        .vehicle-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border-left: 5px solid #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- You can add your logo here -->
            <!-- <img src="https://a2blogistiks.uk/logo.png" alt="A2B Logistiks" class="logo"> -->
            <h1>Your Transport Quote</h1>
            <p>Quote #{{ $quote->id }}</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $quote->customer->user->name }},</p>
            
            <p>Thank you for choosing A2B Logistiks. Your transport quote has been created and is ready for review. We're committed to providing you with a hassle-free vehicle transport experience.</p>
            
            <div class="quote-card">
                <h2 class="section-title">Quote Summary</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Quote ID:</span>
                    <span class="detail-value">{{ $quote->id }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Created On:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($quote->created_at)->format('M d, Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Vehicle Type:</span>
                    <span class="detail-value">{{ $quote->vehicleType->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Estimated Distance:</span>
                    <span class="detail-value">{{ $quote->estimated_distance }} miles</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-{{ $quote->payment_status }}">
                            {{ ucfirst($quote->payment_status) }}
                        </span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Fare:</span>
                    <span class="detail-value price-highlight">£{{ number_format($quote->estimated_fare, 2) }}</span>
                </div>
            </div>
            
            <div class="vehicle-details">
                <h2 class="section-title">Vehicle Information</h2>
                
                @if($quote->vehicle_make || $quote->vehicle_model)
                <div class="detail-row">
                    <span class="detail-label">Vehicle:</span>
                    <span class="detail-value">{{ $quote->vehicle_make }} {{ $quote->vehicle_model }}</span>
                </div>
                @endif
                
                @if($quote->number_plate)
                <div class="detail-row">
                    <span class="detail-label">Registration Number:</span>
                    <span class="detail-value">{{ $quote->number_plate }}</span>
                </div>
                @endif
                
                @if($quote->gearbox)
                <div class="detail-row">
                    <span class="detail-label">Transmission:</span>
                    <span class="detail-value">{{ ucfirst($quote->gearbox) }}</span>
                </div>
                @endif
                
                @if($quote->seating_capacity)
                <div class="detail-row">
                    <span class="detail-label">Seating Capacity:</span>
                    <span class="detail-value">{{ $quote->seating_capacity }} seats</span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Vehicle Available From:</span>
                    <span class="detail-value">
                        {{ $quote->vehicle_available_from ? \Carbon\Carbon::parse($quote->vehicle_available_from)->format('M d, Y') : 'Not specified' }}
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Vehicle Available To:</span>
                    <span class="detail-value">
                        {{ $quote->vehicle_available_to ? \Carbon\Carbon::parse($quote->vehicle_available_to)->format('M d, Y') : 'Not specified' }}
                    </span>
                </div>
            </div>
            
            <div class="location-details">
                <h2 class="section-title">Transport Details</h2>
                
                <div class="location-card">
                    <div class="location-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill="#1a3c8a"/>
                        </svg>
                        <span>Pickup Location ({{ ucfirst($quote->collection_place_type) }})</span>
                    </div>
                    <div class="location-address">{{ $quote->pickup_locations['text'] }}</div>
                </div>
                
                <div class="location-card">
                    <div class="location-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill="#1a3c8a"/>
                        </svg>
                        <span>Dropoff Location ({{ ucfirst($quote->delivery_place_type) }})</span>
                    </div>
                    <div class="location-address">{{ $quote->dropoff_locations['text'] }}</div>
                </div>
            </div>
            
            <div class="contact-details">
                <h2 class="section-title">Contact Information</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Collection Contact:</span>
                    <span class="detail-value">{{ $quote->collection_contact_name ?: 'Not specified' }}</span>
                </div>
                
                @if($quote->collection_contact_phone)
                <div class="detail-row">
                    <span class="detail-label">Collection Phone:</span>
                    <span class="detail-value">{{ $quote->collection_contact_phone }}</span>
                </div>
                @endif
                
                @if($quote->collection_contact_email)
                <div class="detail-row">
                    <span class="detail-label">Collection Email:</span>
                    <span class="detail-value">{{ $quote->collection_contact_email }}</span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Delivery Contact:</span>
                    <span class="detail-value">{{ $quote->delivery_contact_name ?: 'Not specified' }}</span>
                </div>
                
                @if($quote->delivery_contact_phone)
                <div class="detail-row">
                    <span class="detail-label">Delivery Phone:</span>
                    <span class="detail-value">{{ $quote->delivery_contact_phone }}</span>
                </div>
                @endif
                
                @if($quote->delivery_contact_email)
                <div class="detail-row">
                    <span class="detail-label">Delivery Email:</span>
                    <span class="detail-value">{{ $quote->delivery_contact_email }}</span>
                </div>
                @endif
            </div>
            
            @if($quote->comments)
            <div class="quote-card">
                <h2 class="section-title">Additional Comments</h2>
                <p>{{ $quote->comments }}</p>
            </div>
            @endif
            
            <p>To proceed with your booking, please review the quote details and make a payment by clicking the button below. Upon payment, your vehicle transport will be scheduled according to the available dates provided.</p>
            
            <div class="cta-container">
                <a href="{{ $paymentLink }}" class="cta-button">Pay Now</a>
            </div>
            
            <p>If you have any questions or need to make changes to your quote, please don't hesitate to contact our customer support team at <a href="mailto:info@a2blogistiks.uk">info@a2blogistiks.uk</a> or call us at <a href="tel:+447398229432">+44 7398 229432</a>.</p>
            
            <p>Thank you for choosing A2B Logistiks!</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} A2B Logistiks UK. All rights reserved.</p>
            <p>This is an automated email, please do not reply directly.</p>
            
            {{-- <div class="social-links">
                <a href="https://facebook.com/a2blogistiks">Facebook</a>
                <a href="https://twitter.com/a2blogistiks">Twitter</a>
                <a href="https://instagram.com/a2blogistiks">Instagram</a>
            </div>
            
            <p>
                <a href="https://www.a2blogistiks.uk/terms">Terms of Service</a> | 
                <a href="https://www.a2blogistiks.uk/privacy">Privacy Policy</a>
            </p> --}}
        </div>
    </div>
</body>
</html>
<?php
/**
 * Consultation Success Page
 */

require_once '../settings/core.php';

if (!isset($_SESSION['customer_id']) || !isset($_GET['consultation_id'])) {
    header("Location: all_products.php");
    exit;
}

$consultation_id = $_GET['consultation_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            padding: 48px;
            text-align: center;
            max-width: 600px;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #d1fae5;
            color: #065f46;
            font-size: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        
        .success-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-dark);
            margin-bottom: 16px;
        }
        
        .success-message {
            color: var(--gray-medium);
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        .consultation-details {
            background: var(--gray-light);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--cream);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--gray-dark);
        }
        
        .detail-value {
            color: var(--gray-medium);
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        <h1 class="success-title">Booking Confirmed!</h1>
        <p class="success-message">
            Your consultation has been successfully booked and payment confirmed. 
            The planner will contact you soon with further details.
        </p>
        
        <div class="consultation-details" id="consultationDetails">
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
        
        <div class="d-flex gap-3 justify-content-center">
            <a href="my_consultations.php" class="btn btn-primary btn-lg">
                <i class="bi bi-calendar-check me-2"></i>View My Consultations
            </a>
            <a href="all_products.php" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Browse Products
            </a>
        </div>
    </div>

    <script>
        const consultationId = <?php echo json_encode($consultation_id); ?>;
        
        // Load consultation details
        fetch('../actions/get_consultation_details_action.php?consultation_id=' + consultationId)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayDetails(data.consultation);
                }
            })
            .catch(error => console.error('Error:', error));
        
        function displayDetails(consultation) {
            const date = new Date(consultation.consultation_date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            const time = formatTime(consultation.consultation_time);
            
            document.getElementById('consultationDetails').innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Planner:</span>
                    <span class="detail-value">${escapeHtml(consultation.planner_name)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Service:</span>
                    <span class="detail-value">${escapeHtml(consultation.service_name)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">${date}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span class="detail-value">${time}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">${consultation.duration_minutes} minutes</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fee Paid:</span>
                    <span class="detail-value">GHS ${parseFloat(consultation.consultation_fee).toFixed(2)}</span>
                </div>
            `;
        }
        
        function formatTime(timeString) {
            const parts = timeString.split(':');
            const hours = parseInt(parts[0]);
            const minutes = parts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${minutes} ${ampm}`;
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>

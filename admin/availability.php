<?php
/**
 * Planner Availability Management
 * Allows planners to set their consultation availability
 */

require_once '../settings/core.php';

if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login/login.php");
    exit;
}

$planner_id = $_SESSION['customer_id'];
$planner_name = $_SESSION['customer_name'] ?? 'Planner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Availability - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafb 0%, #f1f5f9 100%);
        }
        
        .availability-container {
            max-width: 1000px;
            margin: 20px auto 60px;
            padding: 0 20px;
        }
        
        .availability-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(234, 224, 218, 0.6);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(139, 118, 108, 0.08);
            padding: 32px;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
        }
        
        .availability-card h4 {
            font-family: 'Playfair Display', serif;
            color: #2c2c2c;
            font-weight: 600;
        }
        
        .day-slot {
            border: 2px solid rgba(201, 169, 97, 0.25);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .day-slot:hover {
            border-color: #C9A961;
            background: rgba(249, 245, 240, 0.8);
            box-shadow: 0 4px 12px rgba(201, 169, 97, 0.15);
        }
        
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .day-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2c2c2c;
            font-family: 'Playfair Display', serif;
        }
        
        .time-slots {
            display: grid;
            gap: 12px;
        }
        
        .time-slot-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: rgba(248, 250, 251, 0.8);
            border: 1px solid rgba(234, 224, 218, 0.4);
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .time-slot-item:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(201, 169, 97, 0.3);
        }
        
        .slot-time {
            font-weight: 600;
            color: #2c2c2c;
        }
        
        .btn-delete-slot {
            background: #fee2e2;
            color: #991b1b;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-delete-slot:hover {
            background: #fecaca;
        }
        
        .time-slot-item {
            flex-wrap: wrap;
        }
        
        .slot-edit-form {
            margin-right: 12px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="header-container">
                <!-- Left: Logo -->
                <div class="header-left">
                    <a href="../index.php" class="vc-logo">
                        <div class="vc-logo-ring"></div>
                        <div class="vc-logo-text">
                            <div class="vc-logo-main">VendorConnect</div>
                            <div class="vc-logo-sub">GHANA</div>
                        </div>
                    </a>
                </div>
                
                <!-- Center: Page Title -->
                <div class="header-center">
                    <h1 class="page-title">Manage Availability</h1>
                    <p class="page-subtitle">Set your consultation availability times</p>
                </div>
                
                <!-- Right: Navigation -->
                <div class="header-right">
                    <a href="dashboard.php" class="header-nav-btn">
                        <i class="bi bi-grid"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <a href="consultations.php" class="header-nav-btn">
                        <i class="bi bi-calendar-check"></i>
                        <span class="nav-label">Consultations</span>
                    </a>
                    <a href="../login/logout.php" class="header-nav-btn logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="nav-label">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="availability-container">
        <div class="availability-card">
            <h4 class="mb-4">Set Your Availability</h4>
            <p class="text-muted mb-4">Choose which days you're available and set your working hours</p>
            <form id="addSlotForm">
                <div class="mb-4">
                    <label class="form-label fw-bold">Select Days</label>
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input day-checkbox" type="checkbox" value="0" id="day0">
                                <label class="form-check-label" for="day0">Sunday</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input day-checkbox" type="checkbox" value="1" id="day1">
                                <label class="form-check-label" for="day1">Monday</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input day-checkbox" type="checkbox" value="2" id="day2">
                                <label class="form-check-label" for="day2">Tuesday</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input day-checkbox" type="checkbox" value="3" id="day3">
                                <label class="form-check-label" for="day3">Wednesday</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input day-checkbox" type="checkbox" value="4" id="day4">
                                <label class="form-check-label" for="day4">Thursday</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input day-checkbox" type="checkbox" value="5" id="day5">
                                <label class="form-check-label" for="day5">Friday</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input class="form-check-input day-checkbox" type="checkbox" value="6" id="day6">
                                <label class="form-check-label" for="day6">Saturday</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Start Time</label>
                        <select class="form-select" id="startTime" required>
                            <option value="00:00">12:00 AM</option>
                            <option value="01:00">1:00 AM</option>
                            <option value="02:00">2:00 AM</option>
                            <option value="03:00">3:00 AM</option>
                            <option value="04:00">4:00 AM</option>
                            <option value="05:00">5:00 AM</option>
                            <option value="06:00">6:00 AM</option>
                            <option value="07:00">7:00 AM</option>
                            <option value="08:00">8:00 AM</option>
                            <option value="09:00" selected>9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="17:00">5:00 PM</option>
                            <option value="18:00">6:00 PM</option>
                            <option value="19:00">7:00 PM</option>
                            <option value="20:00">8:00 PM</option>
                            <option value="21:00">9:00 PM</option>
                            <option value="22:00">10:00 PM</option>
                            <option value="23:00">11:00 PM</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">End Time</label>
                        <select class="form-select" id="endTime" required>
                            <option value="00:00">12:00 AM</option>
                            <option value="01:00">1:00 AM</option>
                            <option value="02:00">2:00 AM</option>
                            <option value="03:00">3:00 AM</option>
                            <option value="04:00">4:00 AM</option>
                            <option value="05:00">5:00 AM</option>
                            <option value="06:00">6:00 AM</option>
                            <option value="07:00">7:00 AM</option>
                            <option value="08:00">8:00 AM</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="17:00" selected>5:00 PM</option>
                            <option value="18:00">6:00 PM</option>
                            <option value="19:00">7:00 PM</option>
                            <option value="20:00">8:00 PM</option>
                            <option value="21:00">9:00 PM</option>
                            <option value="22:00">10:00 PM</option>
                            <option value="23:00">11:00 PM</option>
                            <option value="23:59">11:59 PM</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-2"></i>Save Availability
                        </button>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Select the days you're available and set your working hours. These times will apply to all selected days.</small>
                </div>
            </form>
        </div>

        <div class="availability-card">
            <h4 class="mb-4">Your Availability Schedule</h4>
            <div id="slotsContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3">Loading your availability...</p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="consultations.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-calendar-check me-2"></i>View Consultations
            </a>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        document.addEventListener('DOMContentLoaded', function() {
            loadSlots();
        });

        // Add new slots
        document.getElementById('addSlotForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get selected days
            const selectedDays = [];
            document.querySelectorAll('.day-checkbox:checked').forEach(checkbox => {
                selectedDays.push(checkbox.value);
            });
            
            if (selectedDays.length === 0) {
                alert('Please select at least one day');
                return;
            }
            
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            
            // Validate times
            if (startTime >= endTime) {
                alert('End time must be after start time');
                return;
            }
            
            // Add slot for each selected day
            const promises = selectedDays.map(day => {
                const data = {
                    day_of_week: day,
                    start_time: startTime,
                    end_time: endTime
                };
                
                return fetch('../actions/add_time_slot_action.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
            });
            
            Promise.all(promises)
                .then(responses => Promise.all(responses.map(r => r.json())))
                .then(results => {
                    const allSuccess = results.every(r => r.status === 'success');
                    if (allSuccess) {
                        alert('Availability saved successfully!');
                        document.getElementById('addSlotForm').reset();
                        document.querySelectorAll('.day-checkbox').forEach(cb => cb.checked = false);
                        loadSlots();
                    } else {
                        alert('Some slots failed to save. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to save availability');
                });
        });

        // Load all slots
        function loadSlots() {
            fetch('../actions/get_planner_slots_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displaySlots(data.slots);
                    } else {
                        document.getElementById('slotsContainer').innerHTML = 
                            '<p class="text-center text-muted">No availability slots set yet. Add some above!</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('slotsContainer').innerHTML = 
                        '<p class="text-center text-danger">Failed to load slots</p>';
                });
        }

        // Display slots grouped by day
        function displaySlots(slots) {
            if (!slots || slots.length === 0) {
                document.getElementById('slotsContainer').innerHTML = 
                    '<p class="text-center text-muted">No availability slots set yet. Add some above!</p>';
                return;
            }
            
            // Group by day
            const slotsByDay = {};
            slots.forEach(slot => {
                if (!slotsByDay[slot.day_of_week]) {
                    slotsByDay[slot.day_of_week] = [];
                }
                slotsByDay[slot.day_of_week].push(slot);
            });
            
            // Sort days
            const sortedDays = Object.keys(slotsByDay).sort((a, b) => a - b);
            
            let html = '';
            sortedDays.forEach(day => {
                html += `
                    <div class="day-slot">
                        <div class="day-header">
                            <div class="day-name">${dayNames[day]}</div>
                        </div>
                        <div class="time-slots">
                `;
                
                slotsByDay[day].forEach(slot => {
                    html += `
                        <div class="time-slot-item" id="slot-${slot.slot_id}">
                            <div class="slot-time" id="time-${slot.slot_id}">
                                ${formatTime(slot.start_time)} - ${formatTime(slot.end_time)}
                            </div>
                            <div class="slot-edit-form" id="edit-${slot.slot_id}" style="display: none; flex: 1;">
                                <div style="display: flex; gap: 12px; align-items: center;">
                                    <select class="form-select form-select-sm" id="edit-start-${slot.slot_id}" style="width: 140px;">
                                        ${generateTimeOptions(slot.start_time)}
                                    </select>
                                    <span>to</span>
                                    <select class="form-select form-select-sm" id="edit-end-${slot.slot_id}" style="width: 140px;">
                                        ${generateTimeOptions(slot.end_time)}
                                    </select>
                                    <button class="btn btn-sm btn-success" onclick="saveEdit(${slot.slot_id}, ${slot.day_of_week})">
                                        <i class="bi bi-check-lg"></i> Save
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="cancelEdit(${slot.slot_id})">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2" onclick="editSlot(${slot.slot_id})" id="edit-btn-${slot.slot_id}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn-delete-slot" onclick="deleteSlot(${slot.slot_id})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('slotsContainer').innerHTML = html;
        }

        // Delete slot
        function deleteSlot(slotId) {
            if (!confirm('Are you sure you want to delete this time slot?')) {
                return;
            }
            
            fetch('../actions/delete_time_slot_action.php?slot_id=' + slotId, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Time slot deleted successfully!');
                    loadSlots();
                } else {
                    alert(data.message || 'Failed to delete time slot');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete time slot');
            });
        }

        // Format time
        function formatTime(timeString) {
            const parts = timeString.split(':');
            const hours = parseInt(parts[0]);
            const minutes = parts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${minutes} ${ampm}`;
        }

        // Generate time options for edit dropdown
        function generateTimeOptions(selectedTime) {
            const times = [
                '00:00', '01:00', '02:00', '03:00', '04:00', '05:00',
                '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
                '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
                '18:00', '19:00', '20:00', '21:00', '22:00', '23:00', '23:59'
            ];
            
            return times.map(time => {
                const selected = time === selectedTime ? 'selected' : '';
                const parts = time.split(':');
                const hours = parseInt(parts[0]);
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const displayHours = hours % 12 || 12;
                const label = `${displayHours}:${parts[1]} ${ampm}`;
                return `<option value="${time}" ${selected}>${label}</option>`;
            }).join('');
        }

        // Edit slot
        function editSlot(slotId) {
            document.getElementById(`time-${slotId}`).style.display = 'none';
            document.getElementById(`edit-${slotId}`).style.display = 'flex';
            document.getElementById(`edit-btn-${slotId}`).style.display = 'none';
        }

        // Cancel edit
        function cancelEdit(slotId) {
            document.getElementById(`time-${slotId}`).style.display = 'block';
            document.getElementById(`edit-${slotId}`).style.display = 'none';
            document.getElementById(`edit-btn-${slotId}`).style.display = 'inline-block';
        }

        // Save edit
        function saveEdit(slotId, dayOfWeek) {
            const startTime = document.getElementById(`edit-start-${slotId}`).value;
            const endTime = document.getElementById(`edit-end-${slotId}`).value;
            
            if (startTime >= endTime) {
                alert('End time must be after start time');
                return;
            }
            
            // Delete old slot and create new one
            fetch('../actions/delete_time_slot_action.php?slot_id=' + slotId, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Add new slot with updated times
                    return fetch('../actions/add_time_slot_action.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            day_of_week: dayOfWeek,
                            start_time: startTime,
                            end_time: endTime
                        })
                    });
                } else {
                    throw new Error('Failed to delete old slot');
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Time slot updated successfully!');
                    loadSlots();
                } else {
                    alert('Failed to update time slot');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update time slot');
            });
        }
    </script>
</body>
</html>

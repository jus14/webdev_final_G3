<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../public/index.php");
    exit;
}

// Validate incoming POST data
if (!isset($_POST['doctor_id'], $_POST['appointment_date'], $_POST['hour'], $_POST['minute'], $_POST['ampm'])) {
    echo "Missing required booking data.";
    exit;
}

$user_id = $_SESSION['user']['id'];
$doctor_id = (int)$_POST['doctor_id'];
$date = $_POST['appointment_date'];
$hour = (int)$_POST['hour'];
$minute = (int)$_POST['minute'];
$ampm = $_POST['ampm'];

// Convert to 24-hour time
if ($ampm === 'PM' && $hour != 12) $hour += 12;
if ($ampm === 'AM' && $hour == 12) $hour = 0;

$time = sprintf('%02d:%02d:00', $hour, $minute);
$appointment_datetime_str = $date . ' ' . $time;

$appointment_dt = DateTime::createFromFormat('Y-m-d H:i:s', $appointment_datetime_str);
$current_dt = new DateTime();

if (!$appointment_dt) {
    echo "Invalid appointment time format.";
    exit;
}

// Check if appointment is in the past
if ($appointment_dt < $current_dt) {
    header("Location: ../public/invalid_time.php");
    exit;
}

// Fetch doctor's working hours
$doctor_sql = "SELECT start_time, end_time FROM doctors WHERE id = ?";
$doctor_stmt = $conn->prepare($doctor_sql);
$doctor_stmt->bind_param("i", $doctor_id);
$doctor_stmt->execute();
$doctor_result = $doctor_stmt->get_result();

if ($doctor_result->num_rows === 0) {
    echo "Doctor not found.";
    exit;
}

$doctor = $doctor_result->fetch_assoc();
$start_dt = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $doctor['start_time']);
$end_dt = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $doctor['end_time']);

if (!$start_dt || !$end_dt) {
    echo "Invalid doctor schedule format.";
    exit;
}

// Check if appointment is within working hours
if ($appointment_dt < $start_dt || $appointment_dt >= $end_dt) {
    header("Location: ../public/outside_hours.php");
    exit;
}

// Check for conflicting appointments
$check_sql = "SELECT 1 FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("iss", $doctor_id, $date, $time);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    header("Location: ../public/booking_conflict.php");
    exit;
}

// Insert the appointment
$sql = "INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time)
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $doctor_id, $date, $time);

if ($stmt->execute()) {
    header("Location: ../public/appointment_success.php");
    exit;
} else {
    error_log("Booking error: " . $stmt->error);
    header("Location: ../public/appointment_error.php");
    exit;
}

$stmt->close();
$conn->close();
?>
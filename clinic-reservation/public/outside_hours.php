<?php
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invalid Appointment Time</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #1e1e2f;
            color: #fff;
            text-align: center;
            padding: 100px;
        }
        .card {
            background: #2e2e40;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 15px rgba(255, 0, 100, 0.3);
        }
        h1 {
            color: #ff007a;
        }
        a {
            color: #00d2ff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>❌ Out of Hours</h1>
        <p>Please choose a time within the clinic's available schedule.</p>
        <a href="javascript:history.back()">← Go Back</a>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Booking Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
            font-size: 24px;
        }
        p {
            color: #555555;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Table Booking Confirmation</h1>
        <p>Hello,</p>
        <p>You have been invited to a table booking at our cafe. Here are the details:</p>
        <p><strong>Table ID:</strong> {{ $bookedTable->table_id }}</p>
        <p><strong>From:</strong> {{ $bookedTable->time_from->format('Y-m-d H:i:s') }}</p>
        <p><strong>To:</strong> {{ $bookedTable->time_to->format('Y-m-d H:i:s') }}</p>
        <p>Please confirm your attendance by clicking the button below:</p>
        <a href="#" class="btn">Confirm Booking</a>
        <p>Thank you!</p>
    </div>
</body>
</html>

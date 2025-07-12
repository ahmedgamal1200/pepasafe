<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري العمل</title>
    <!-- إضافة مكتبة Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- إضافة خط Tajawal من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #ffffff;
            font-family: 'Tajawal', Arial, sans-serif;
            direction: rtl;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .icon {
            font-size: 150px;
            color: #f39c12;
            animation: pulse 2s infinite;
            margin-bottom: 20px;
        }
        .message {
            font-size: 36px;
            color: #2c3e50;
        }
        /* تأثير حركي للأيقونة */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
<div class="container">
    <i class="fas fa-tools icon"></i>
    <div class="message">جاري العمل على بعض الإصلاحات</div>
</div>
</body>
</html>

<?php
// This file will hold your SMTP settings
return [
    'smtp' => [
        'host' => 'smtp.example.com', // Your SMTP server
        'username' => 'your_email@example.com',
        'password' => 'your_app_password',
        'port' => 587, // or 465 for SSL
        'encryption' => 'tls', // or 'ssl'
        'from_email' => 'noreply@easil.com',
        'from_name' => 'EASIL Platform'
    ]
];
<?php
// config/firebase.php

return [
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('firebase/halalytics-firebase-adminsdk.json')),
    ],
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],
];

<?php

return [
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('firebase/halalytics-firebase-adminsdk.json')),
    ],
    'credentials_path' => env('FIREBASE_CREDENTIALS', storage_path('firebase/halalytics-firebase-adminsdk.json')),
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],
];

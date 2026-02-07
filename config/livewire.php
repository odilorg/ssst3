<?php

return [

    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'layout' => 'components.layouts.app',
    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads - SECURED
    |---------------------------------------------------------------------------
    | SECURITY: Strict file upload validation to prevent malicious uploads.
    */

    'temporary_file_upload' => [
        'disk' => 'local',
        'rules' => [
            'required',
            'file',
            'max:10240', // 10MB max
            'mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,csv,txt,zip,mp4,mov,mp3,wav',
            'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain,application/zip,video/mp4,video/quicktime,audio/mpeg,audio/wav',
        ],
        'directory' => 'livewire-tmp',
        'middleware' => 'throttle:5,1', // 5 uploads per minute
        'preview_mimes' => [
            'png', 'gif', 'jpg', 'jpeg', 'webp', 'svg',
            'mp4', 'mov', 'mp3', 'wav',
        ],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],

    'render_on_redirect' => false,
    'legacy_model_binding' => false,
    'inject_assets' => true,

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    'inject_morph_markers' => true,
    'smart_wire_keys' => false,
    'pagination_theme' => 'tailwind',
    'release_token' => 'a',
];

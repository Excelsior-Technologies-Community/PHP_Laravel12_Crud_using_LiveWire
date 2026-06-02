<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel 12 Livewire CRUD - Post Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @livewireStyles
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container-custom {
            max-width: 1400px;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .object-cover {
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container container-custom py-4">
        <div class="text-center mb-4">
            <h1 class="text-white">
                <i class="fas fa-blog"></i> Laravel 12 Livewire CRUD
            </h1>
            <p class="text-white-50">Create, Read, Update, Delete posts with image upload - No page reload!</p>
        </div>

        @livewire('posts')
    </div>

    @livewireScripts
</body>
</html>
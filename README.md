# PHP_Laravel12_Crud_using_LiveWire


The main purpose of this project is to perform CRUD operations (Create, Read, Update, Delete) on posts without page reload using Livewire.

Livewire helps to create dynamic and interactive UI using Blade and PHP only, without writing JavaScript.

## Project Highlights:

### This project is useful for beginners to understand:

Laravel MVC structure

Livewire components

Database operations using Eloquent ORM

Real-time UI updates without page refresh



## Technologies Used

PHP 8+

Laravel 12

Livewire

MySQL

Bootstrap 4

Blade Template Engine


---

# Project SetUp

---

## STEP 1: Create Laravel 12 Project

Command:

```
composer create-project laravel/laravel PHP_Laravel12_Crud_using_LiveWire "12.*"
```

Go inside project:
```
cd PHP_Laravel12_Crud_using_LiveWire
```

Run server:
```
php artisan serve
```

Explanation:

Creates a fresh Laravel 12 project
This sets up a fresh Laravel 12 project.





## STEP 2: Configure Database

### Open .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=livewire_crud
DB_USERNAME=root
DB_PASSWORD=

```

Create database in phpMyAdmin:
```
livewire_crud
```

Explanation:

Connects Laravel to your MySQL database

Make sure export_api_db exists in phpMyAdmin



## STEP 3: Create Model + Migration

We will export posts data.

### Command:


```

php artisan make:model Post -m

```

Explanation:

Creates Post model → app/Models/Post.php

Creates Migration → database/migrations/xxxx_create_posts_table.php



### Migration File

 database/migrations/xxxx_create_posts_table.php
```

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

```


### Run migration:
```
php artisan migrate

```

Explanation:

Creates posts table in database



### app/model/Post.php

```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body'];
}

```




## Step 4: Install Livewire

Run Command:

```
composer require livewire/livewire

```

## Step 5: Create Livewire Component

Run Command:

```
php artisan make:livewire posts

```

This creates:

app/Livewire/Posts.php
resources/views/livewire/posts.blade.php


## Step 6: Add Logic to Livewire Component

### Edit:

```

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class Posts extends Component
{
    public $posts, $title, $body, $post_id;
    public $updateMode = false;

    public function render()
    {
        $this->posts = Post::all();
        return view('livewire.posts');
    }

    private function resetInput()
    {
        $this->title = '';
        $this->body = '';
    }

    public function store()
    {
        $validated = $this->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        Post::create($validated);

        session()->flash('message', 'Post Created Successfully.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->updateMode = true;
    }

    public function cancel()
    {
        $this->updateMode = false;
        $this->resetInput();
    }

    public function update()
    {
        $validated = $this->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $post = Post::find($this->post_id);
        $post->update($validated);

        session()->flash('message', 'Post Updated Successfully.');
        $this->updateMode = false;
        $this->resetInput();
    }

    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }
}

```

This handles reading, creating, editing, updating, and deleting posts with Livewire.




## Step 7: Create Blade Views

### resources/views/livewire/posts.blade.php

```
<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if($updateMode)
        @include('livewire.update')
    @else
        @include('livewire.create')
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>No.</th>
                <th>Title</th>
                <th>Body</th>
                <th width="150px">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr>
                <td>{{ $post->id }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->body }}</td>
                <td>
                    <button wire:click="edit({{ $post->id }})" class="btn btn-primary btn-sm">Edit</button>
<button
    class="btn btn-danger btn-sm"
    onclick="confirm('Are you sure you want to delete this post?') || event.stopImmediatePropagation()"
    wire:click="delete({{ $post->id }})"
>
    Delete
</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

```


### resources/views/livewire/create.blade.php

```

<form>
    <div class="form-group">
        <label>Title:</label>
        <input type="text" class="form-control" wire:model="title">
        @error('title') <span class="text-danger">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label>Body:</label>
        <textarea class="form-control" wire:model="body"></textarea>
        @error('body') <span class="text-danger">{{ $message }}</span>@enderror
    </div>

    <button wire:click.prevent="store()" class="btn btn-success">Save</button>
</form>

```

### 


### resources/views/livewire/update.blade.php

```

<form>
    <div class="form-group">
        <label>Title:</label>
        <input type="text" class="form-control" wire:model="title">
        @error('title') <span class="text-danger">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
        <label>Body:</label>
        <textarea class="form-control" wire:model="body"></textarea>
        @error('body') <span class="text-danger">{{ $message }}</span>@enderror
    </div>

    <button wire:click.prevent="update()" class="btn btn-dark">Update</button>
    <button wire:click.prevent="cancel()" class="btn btn-danger">Cancel</button>
</form>

```

### resources/views/posts.blade.php

```

<!DOCTYPE html>
<html>
<head>
    <title>Laravel 12 Livewire CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Laravel 12 Livewire CRUD</h2>

        <livewire:posts />
    </div>

    @livewireScripts
</body>
</html>

```


## STEP 8: Routes

### File: routes/web.php

Defines routes :

```

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('posts'); 
});

```


## Step 9:Run Your Laravel App

### Start the development server:

```
php artisan serve

```

### Then open in browser:

```
 http://localhost:8000

```


## So you can see this type output:

### Posts Page:


<img width="1919" height="962" alt="Screenshot 2026-01-07 164535" src="https://github.com/user-attachments/assets/8cee5b32-9557-4418-8a1f-eec940b57846" />


### Posts Page(Error show):


<img width="1912" height="971" alt="Screenshot 2026-01-07 164703" src="https://github.com/user-attachments/assets/31ff73c4-aefa-46dd-a2b6-061d187cd7fd" />


### Create Page:


<img width="1919" height="963" alt="Screenshot 2026-01-07 164729" src="https://github.com/user-attachments/assets/ccf4bad0-6fba-42ac-8975-726adf04507c" />


(after create success message show)

<img width="1919" height="964" alt="Screenshot 2026-01-07 164740" src="https://github.com/user-attachments/assets/95c0a375-841d-4b74-84c3-d5bd3d358eae" />



### Edit Page:


<img width="1919" height="966" alt="Screenshot 2026-01-07 164806" src="https://github.com/user-attachments/assets/eb0e0dca-fe35-4713-a52b-2f7e4748b444" />

(after update success message show)

<img width="1919" height="973" alt="Screenshot 2026-01-07 164818" src="https://github.com/user-attachments/assets/2c68778f-df5e-4ae7-9f85-a9135f35be22" />



### Delete Page:


<img width="1913" height="965" alt="Screenshot 2026-01-07 165242" src="https://github.com/user-attachments/assets/cd11d530-9e43-4cea-bd38-b010f736675d" />



---


# Project Folder Structure:

```

PHP_Laravel12_Crud_using_LiveWire
│
├── app
│   ├── Livewire
│   │   └── Posts.php
│   ├── Models
│   │   └── Post.php
│   └── Providers
│
├── bootstrap
│   └── app.php
│
├── config
│   └── (all config files)
│
├── database
│   ├── migrations
│   │   └── xxxx_xx_xx_create_posts_table.php
│   ├── seeders
│   └── factories
│
├── public
│   └── index.php
│
├── resources
│   ├── views
│   │   ├── livewire
│   │   │   ├── posts.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── update.blade.php
│   │   └── posts.blade.php
│   ├── css
│   └── js
│
├── routes
│   └── web.php
│
├── storage
│   └── (logs, cache, sessions)
│
├── tests
│
├── .env
├── artisan
├── composer.json
├── package.json
└── README.md

```

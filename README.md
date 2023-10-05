
# Website Frontend Editor ( Laravel )

This package allow you to edit your static text and images from frontend.

## Installation

To install the package

```bash
  composer require sbx/frontcrm
```
Add `Sbx\Frontcrm\Providers\CRMServiceProvider::class` in your project `app/config.php` under providers array.

## Run

```bash
  php artisan migrate:refresh --path=vendor/sbx/frontcrm/src/database/migrations/2023_09_28_174935_create_sbx_settings_table.php
```


## Configuration

Add `@include('frontcrm::editor', ['editable'=> true])` in your view page before `</body>` tag see example below.

```bash
<html>
<head>
    ...
</head>
<body>
    ...
    My content
    ...
    My scripts
    ...
    @if(Auth::check())
      @include('frontcrm::editor', ['editable'=> true])
    @else
      @include('frontcrm::editor', ['editable'=> false])
    @endif
</body>
</html>
```

you can define who will edit and who can only see the changes by define `editable` key `true` or `false`.

and Add `sbx-inline-editor` class in your editable text elements.

Add `sbx-inline-img-editor` class in your editable `img` tags.

See example below:

```bash
<html>
<head>
    ...
</head>
<body>
    <div class="section1">  
      
      <h1 class="sbx-inline-editor">This is the H1 Editable Tag.</h1>

      <img src="your image link" class="sbx-inline-img-editor" />

    <div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @if(Auth::check())
      @include('frontcrm::editor', ['editable'=> true])
    @else
      @include('frontcrm::editor', ['editable'=> false])
    @endif

</body>
</html>
```

## Note:

JQuery is required.



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
  <div id="app"></div>
  <script src="{{ asset('js/ui.js') }}"></script>
  <script src="{{ asset('js/init.js') }}"></script>
</body>

</html>
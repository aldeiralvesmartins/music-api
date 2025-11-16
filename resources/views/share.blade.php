<html>
<head>
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ $product->description }}">
    <meta property="og:image" content="{{ $product->image }}">
    <meta property="og:url" content="{{ url('/produto/'.$product->id) }}">
</head>
<body>
<h1>{{ $product->name }}</h1>
</body>
</html>

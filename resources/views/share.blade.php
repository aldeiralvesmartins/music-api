<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }}</title>

    <!-- Open Graph (para WhatsApp, FB, Telegram) -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ $product->description }}">

    @if(isset($product->images[0]))
        <meta property="og:image" content="{{ $product->images[0]->url }}">
    @endif

    <meta property="og:url" content="{{ url('/produto/'.$product->id) }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="product:price:amount" content="{{ number_format($product->price, 2, '.', '') }}">
    <meta property="product:price:currency" content="BRL">
    <meta property="product:category" content="{{ $product->category->name ?? '' }}">

    <!-- Redirecionamento automático para o frontend -->
    <meta http-equiv="refresh" content="0;url={{ env('HOST_FRONT').'/product/'.$product->id }}">
</head>
<body>
<p>Redirecionando para o produto...</p>
<a href="{{ env('HOST_FRONT').'/product/'.$product->id }}">Clique aqui se não redirecionar</a>
</body>
</html>

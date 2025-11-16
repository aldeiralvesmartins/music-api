<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }}</title>

    <!-- Open Graph -->
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

    <script>
        // Redireciona somente usuários humanos
        document.addEventListener("DOMContentLoaded", function() {
            // Pequeno delay opcional para garantir OG lido pelos bots
            setTimeout(function() {
                window.location.href = "{{ env('HOST_FRONT') . '/product/' . $product->id }}";
            }, 100);
        });
    </script>
</head>
<body style="font-family: sans-serif; text-align: center; padding: 50px;">

<a href="{{ env('HOST_FRONT').'/product/'.$product->id }}"
   style="display:inline-block; padding: 15px 30px; background: #007BFF; color: #fff; text-decoration:none; border-radius: 6px; font-weight:bold;">
    Ver Produto
</a>
</body>
</html>

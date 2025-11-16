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
<body>
<p>Redirecionando para o produto...</p>
<a href="{{ env('HOST_FRONT').'/product/'.$product->id }}">Clique aqui se não redirecionar</a>
</body>
</html>

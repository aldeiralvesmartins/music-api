<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }}</title>

    <!-- Open Graph (Facebook, WhatsApp, etc) -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ $product->description }}">

    <!-- Imagem principal do produto -->
    @if(isset($product->images[0]))
        <meta property="og:image" content="{{ $product->images[0]->url }}">
    @endif

    <meta property="og:url" content="{{ url('/produto/'.$product->id) }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <!-- Preço e categoria -->
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
<h1>{{ $product->name }}</h1>
<p>{{ $product->description }}</p>
<p><strong>Categoria:</strong> {{ $product->category->name ?? 'Sem categoria' }}</p>
<p><strong>Preço:</strong> R$ {{ number_format($product->price, 2, ',', '.') }}</p>

@if(isset($product->images) && count($product->images) > 0)
    <div>
        @foreach($product->images as $img)
            <img src="{{ $img->url }}" alt="{{ $product->name }}" style="max-width:200px; margin:5px;">
        @endforeach
    </div>
@endif
<p>Redirecionando para o produto...</p>
<a href="{{ env('HOST_FRONT').'/product/'.$product->id }}">Clique aqui se não redirecionar</a>
</body>
</html>

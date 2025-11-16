<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            color: white;
        }

        .loading-container {
            text-align: center;
            max-width: 90%;
            width: 500px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .loading-text {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
            font-weight: 300;
        }

        .loading-animation {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .spinner {
            width: 70px;
            height: 70px;
            position: relative;
        }

        .spinner-inner {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top: 4px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .spinner-inner:nth-child(2) {
            border: 4px solid transparent;
            border-bottom: 4px solid rgba(255, 255, 255, 0.7);
            animation: spin-reverse 1.5s linear infinite;
        }

        .spinner-inner:nth-child(3) {
            width: 50%;
            height: 50%;
            top: 25%;
            left: 25%;
            border: 3px solid transparent;
            border-right: 3px solid rgba(255, 255, 255, 0.5);
            animation: spin 2s linear infinite;
        }

        .progress-container {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #ff8a00, #ffcc00);
            border-radius: 10px;
            animation: progress 2s ease-in-out infinite;
        }

        .redirect-text {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-top: 20px;
        }

        .product-preview {
            margin-top: 25px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: left;
        }

        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.2);
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .product-price {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes spin-reverse {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(-360deg); }
        }

        @keyframes progress {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }

        /* Floating elements for background */
        .floating {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
        }

        .floating:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation: float 15s ease-in-out infinite;
        }

        .floating:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            left: 80%;
            animation: float 18s ease-in-out infinite reverse;
        }

        .floating:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 20%;
            left: 85%;
            animation: float 12s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -50px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .loading-container {
                padding: 30px 20px;
            }

            .logo {
                font-size: 2rem;
            }

            .loading-text {
                font-size: 1rem;
            }

            .spinner {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
<!-- Floating background elements -->
<div class="floating"></div>
<div class="floating"></div>
<div class="floating"></div>

<div class="loading-container">
    <div class="logo">{{ config('app.name') }}</div>

    <div class="loading-text pulse">Carregando produto, aguarde um momento...</div>

    <div class="loading-animation">
        <div class="spinner">
            <div class="spinner-inner"></div>
            <div class="spinner-inner"></div>
            <div class="spinner-inner"></div>
        </div>
    </div>

    <div class="progress-container">
        <div class="progress-bar"></div>
    </div>

    @if(isset($product))
        <div class="product-preview">
            @if(isset($product->images[0]))
                <img src="{{ $product->images[0]->url }}" alt="{{ $product->name }}" class="product-image">
            @else
                <div class="product-image"></div>
            @endif
            <div class="product-info">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
            </div>
        </div>
    @endif

    <div class="redirect-text">Você será redirecionado automaticamente</div>
</div>

<script>
    // Redireciona somente usuários humanos
    document.addEventListener("DOMContentLoaded", function() {
        // Pequeno delay para garantir OG lido pelos bots
        setTimeout(function() {
            window.location.href = "{{ env('HOST_FRONT') . '/product/' . $product->id }}";
        }, 3000); // Aumentei para 3 segundos para dar tempo de apreciar a tela de carregamento
    });
</script>
</body>
</html>

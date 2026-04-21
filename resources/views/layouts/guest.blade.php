<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            .login-container {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 20px;
            }
            .login-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                display: grid;
                grid-template-columns: 1fr 1fr;
                max-width: 900px;
                width: 100%;
                overflow: hidden;
            }
            .login-left {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 60px 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: white;
                position: relative;
                overflow: hidden;
            }
            .login-left::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -50%;
                width: 500px;
                height: 500px;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
                border-radius: 50%;
            }
            .login-left::after {
                content: '';
                position: absolute;
                bottom: -30%;
                left: -30%;
                width: 300px;
                height: 300px;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
                border-radius: 50%;
            }
            .login-right {
                padding: 60px 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .logo-container {
                position: relative;
                z-index: 10;
                width: 140px;
                height: 140px;
                background: transparent;
                border-radius: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 40px;
                backdrop-filter: none;
                border: none;
                box-shadow: none;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .logo-container:hover {
                background: transparent;
                border-color: transparent;
                transform: translateY(-5px);
                box-shadow: none;
            }
            .logo-container img {
                width: 95px;
                height: 95px;
                object-fit: contain;
                filter: drop-shadow(0 2px 8px rgba(255, 255, 255, 0.4));
                transition: filter 0.3s ease, transform 0.3s ease;
            }
            .logo-container:hover img {
                filter: drop-shadow(0 4px 16px rgba(255, 255, 255, 0.6));
                transform: scale(1.05);
            }
            .login-title {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 20px;
                text-align: center;
                position: relative;
                z-index: 10;
            }
            .login-subtitle {
                font-size: 14px;
                text-align: center;
                opacity: 0.95;
                line-height: 1.6;
                position: relative;
                z-index: 10;
            }
            @media (max-width: 768px) {
                .login-card {
                    grid-template-columns: 1fr;
                }
                .login-left {
                    padding: 40px 30px;
                    min-height: 250px;
                }
                .login-right {
                    padding: 40px 30px;
                }
                .logo-container {
                    width: 120px;
                    height: 120px;
                    margin-bottom: 30px;
                }
                .logo-container img {
                    width: 80px;
                    height: 80px;
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="login-container">
            <div class="login-card">
                <!-- Left Side - Logo & Title -->
                <div class="login-left">
                    <div class="logo-container">
                        <img src="{{ asset('foto/logobimbel.webp') }}" alt="Logo Bimbel" />
                    </div>
                    <div style="position: relative; z-index: 10; text-align: center;">
                        <div class="login-title">Kantin Alwi</div>
                        <div class="login-subtitle">
                            Sistem manajemen stok yang efisien untuk bisnis Anda. Kelola makanan dan minuman dengan mudah dan cepat.
                        </div>
                    </div>
                </div>

                <!-- Right Side - Form -->
                <div class="login-right">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

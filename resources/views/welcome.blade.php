<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'gairAI') }}</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f7fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .container {
            text-align: center;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 2rem;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('images/GairAIロゴ仮.png') }}" alt="GairAI Logo" style="height: 100px; width: auto;">
        <h1>GairAI</h1>
        <div>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="button">ダッシュボード</a>
                @else
                    <a href="{{ route('login') }}" class="button">ログイン</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="button">新規ユーザー登録</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</body>
</html>
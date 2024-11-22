<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.header')
<div class="auth-container">
    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf
        <h2 class="auth-title">Авторизоваться</h2>

        <input type="email" name="email" placeholder="Электронная почта" required autofocus value="{{ old('email') }}">
        <input type="password" name="password" placeholder="Пароль" required>

        <button type="submit" class="auth-button">Авторизоваться</button>

        @if ($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="auth-link">
            <p>У вас нет учетной записи? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
        </div>
    </form>
</div>
</body>
</html>

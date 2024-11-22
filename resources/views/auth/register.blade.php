<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.header')
<div class="auth-container">
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf
        <h2 class="auth-title">Регистрация</h2>

        <input type="text" name="username" placeholder="Имя пользователя" required autofocus value="{{ old('username') }}">
        <input type="email" name="email" placeholder="Электронная почта" required value="{{ old('email') }}">
        <input type="password" name="password" placeholder="Пароль" required>
        <input type="password" name="password_confirmation" placeholder="Подтвердите пароль" required>
        <input type="number" name="age" placeholder="Возраст" required value="{{ old('age') }}">

        <button type="submit" class="auth-button">Зарегистрироваться</button>

        @if($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="auth-link">
            <p>У вас уже есть учетная запись? <a href="{{ route('login') }}">Авторизоваться</a></p>
        </div>
    </form>
</div>
</body>
</html>

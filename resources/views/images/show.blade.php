<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $image->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/show.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.header')
@php
    $currentUser = Auth::user();
    $userIsAuthenticated = Auth::check();
    $isOwnerOrAdmin = $currentUser && ($currentUser->user_id === $image->user_id || $currentUser->is_admin);
    $userIsAdult = $userIsAuthenticated && Auth::user()->age >= 18;
    $isAdminWithPrivileges = $currentUser && $currentUser->administrator && $currentUser->administrator->privileges_level >= 1;
@endphp

<div class="image-page-container">
    <div class="top-section">
    <div class="image-section">
    {{-- Иконка лайков и количество лайков --}}
    <div class="likes-container">
        @if ($userIsAuthenticated)
            <span id="like-btn" class="icon {{ Auth::user()->hasLiked($image) ? 'fas fa-heart liked' : 'far fa-heart' }}" data-liked="{{ Auth::user()->hasLiked($image) ? 'true' : 'false' }}" data-image-id="{{ $image->image_id }}"></span>
            <span class="likes" id="like-count">{{ count($image->likes) }}</span>
        @else
            <a href="{{ route('login') }}">
                <span class="icon far fa-heart"></span>
            </a>
        @endif
    </div>

    {{-- Иконка избранного --}}
    <div class="favorites-container">
        @if ($userIsAuthenticated)
            <span id="favorite-btn" class="icon {{ Auth::user()->hasFavorited($image) ? 'fas fa-star favorited' : 'far fa-star' }}" data-favorited="{{ Auth::user()->hasFavorited($image) ? 'true' : 'false' }}" data-image-id="{{ $image->image_id }}"></span>
        @else
            <a href="{{ route('login') }}">
                <span class="icon far fa-star"></span>
            </a>
        @endif
    </div>

    {{-- Остальная часть секции изображения --}}
    @if ($image->is_adult && !$userIsAdult)
        <a href="{{ route('login') }}">
            <img src="{{ $image->url }}" alt="{{ $image->title }}" class="displayed-image blurred">
        </a>
    @else
        <a href="#" onclick="openModal(); return false;">
            <img src="{{ $image->url }}" alt="{{ $image->title }}" class="displayed-image">
        </a>
    @endif
</div>

{{-- Остальная часть шаблона ... --}}


        <!-- The Modal -->
        <div id="myModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="modalImage" src="">

            <div id="caption"></div>
        </div>

        <div class="info-section">
            <div class="info-content">
                <h2>{{ $image->title }}</h2>
                <p>Автор: <a href="/profile/{{ $image->user->user_id }}">{{ $image->user->username }}</a></p>
                <p>{{ $image->description }}</p>

                {{-- Теги --}}
                <div class="tags">
                    @foreach ($image->tags as $tag)
                        <a href="{{ route('home.index', ['tag' => $tag->name]) }}" class="tag">{{ $tag->name }}</a>
                    @endforeach
                </div>


                {{-- Удалить и Редактировать --}}
                @if ($currentUser && ($currentUser->user_id === $image->user_id || $isAdminWithPrivileges))
                    <div class="edit-delete-buttons">
                        <div class="button-container">
                            <a href="{{ route('images.edit', $image->image_id) }}" class="button">Редактировать</a>
                        </div>
                        <div class="button-container">
                            <a href="{{ route('images.destroy', $image->image_id) }}" class="button" onclick="event.preventDefault(); if(confirmDelete()) { document.getElementById('delete-form').submit(); }">Удалить</a>
                        </div>

                        <form id="delete-form" action="{{ route('images.destroy', $image->image_id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="comments-section">
    {{-- Форма для добавления комментариев --}}
    @if ($userIsAuthenticated)
        <form action="{{ route('comments.store', $image->image_id) }}" method="post">
            @csrf
            <textarea name="comment" placeholder="Добавьте комментарий..."></textarea>
            <button type="submit">Отправить</button>
        </form>
    @endif

    {{-- Комментарии и ответы на комментарии --}}
    <div class="comments">
        @foreach ($image->comments as $comment)
            @include('images.comment', ['comment' => $comment])
        @endforeach
    </div>
</div>
</div>
<script src="{{ asset('js/show.js') }}"></script>
</body>
</html>


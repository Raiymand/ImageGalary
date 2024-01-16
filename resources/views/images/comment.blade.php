<div class="comment" id="comment-{{ $comment->comment_id }}">
    <p><strong><a href="/profile/{{ $comment->user->user_id }}">{{ $comment->user->username }}</a>:</strong></p>
    <div class="comment-content">{{ $comment->comment }}</div>
    <div class="comment-actions">
        {{-- Кнопка для ответа на комментарий (только для авторизованных пользователей) --}}
        @auth
            <button class="reply-button" onclick="showReplyForm({{ $comment->comment_id }})">Ответить</button>
        @endauth

        {{-- Проверка на владельца комментария --}}
        @if ($currentUser && $currentUser->user_id === $comment->user_id)
            <button class="edit-comment-button" onclick="editComment({{ $comment->comment_id }})">Редактировать</button>
        @endif

        {{-- Проверка на владельца комментария или администратора 1 уровня и выше --}}
        @php
            $canDeleteComment = $currentUser && ($currentUser->user_id === $comment->user_id || 
            ($currentUser->administrator && $currentUser->administrator->privileges_level >= 1));
        @endphp
        @if ($canDeleteComment)
            <button class="delete-comment-button" onclick="deleteComment({{ $comment->comment_id }})">Удалить</button>
        @endif
    </div>

    {{-- Форма для создания ответа на комментарий --}}
    @auth
        <form id="reply-form-{{ $comment->comment_id }}" action="{{ route('comments.store', $image->image_id) }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->comment_id }}">
            <textarea name="comment" required></textarea>
            <button type="submit">Отправить</button>
        </form>
    @endauth

    {{-- Рекурсивное отображение ответов на комментарий --}}
    @foreach ($comment->replies as $reply)
        @include('images.comment', ['comment' => $reply])
    @endforeach
</div>

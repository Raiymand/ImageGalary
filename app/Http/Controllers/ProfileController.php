<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show($userId)
    {
        $user = User::with(['images', 'favorites.image', 'likes.image'])->find($userId);

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Пользователь не найден']);
        }

        // Определение возраста текущего авторизованного пользователя
        $currentUser = Auth::user();
        $userIsAdult = $currentUser && $currentUser->age >= 18;

        // Фильтрация изображений для взрослых для несовершеннолетних пользователей
        if (!$userIsAdult) {
            $user->images = $user->images->where('is_adult', false);
            $user->favorites = $user->favorites->filter(function ($favorite) {
                return !$favorite->image->is_adult;
            });
            $user->likes = $user->likes->filter(function ($like) {
                return !$like->image->is_adult;
            });
        }

        // Передаем данные в представление
        return view('profile.profile', [
            'user' => $user,
            'uploadedImages' => $user->images,
            'favoriteImages' => $user->favorites->pluck('image'),
            'likedImages' => $user->likes->pluck('image')
        ]);
    }


    public function blockUser(Request $request, $userId)
    {
        $admin = Auth::user();

        if (!$admin || !$admin->is_admin) {
            return redirect()->back()->withErrors(['error' => 'Недостаточно прав']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Пользователь не найден']);
        }

        // Устанавливаем дату блокировки пользователя
        $user->blocked_until = now()->addDays($request->input('days', 30)); // По умолчанию блокируем на 30 дней
        $user->save();

        return redirect()->back()->with('success', 'Пользователь заблокирован');
    }
}
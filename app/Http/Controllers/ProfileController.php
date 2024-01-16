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
    
        $currentUser = Auth::user();
        $userIsAdult = $currentUser && $currentUser->age >= 18;
        $userIsAuthenticated = Auth::check();
    
        // Фильтрация изображений для взрослых для несовершеннолетних пользователей
        if ($userIsAuthenticated && !$userIsAdult) {
            $user->images = $user->images->where('is_adult', false);
            $user->favorites = $user->favorites->filter(function ($favorite) {
                return !$favorite->image->is_adult;
            });
            $user->likes = $user->likes->filter(function ($like) {
                return !$like->image->is_adult;
            });
        }
    
        // Определяем, следует ли применять блюр к изображениям для взрослых
        $showBlur = !$userIsAuthenticated;

        // Установка свойства show_blur для каждого изображения
        $user->images->each(function ($image) use ($showBlur) {
            $image->show_blur = $showBlur && $image->is_adult;
        });
        $user->favorites->each(function ($favorite) use ($showBlur) {
            $favorite->image->show_blur = $showBlur && $favorite->image->is_adult;
        });
        $user->likes->each(function ($like) use ($showBlur) {
            $like->image->show_blur = $showBlur && $like->image->is_adult;
        });
    
        return view('profile.profile', [
            'user' => $user,
            'uploadedImages' => $user->images,
            'favoriteImages' => $user->favorites->pluck('image'),
            'likedImages' => $user->likes->pluck('image'),
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
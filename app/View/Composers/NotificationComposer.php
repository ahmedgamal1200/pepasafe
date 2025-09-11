<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationComposer
{
    public function compose(View $view)
    {
        if (Auth::check()) {
            $notifications = Auth::user()->unreadNotifications()->get();
        } else {
            $notifications = collect();
        }
        $view->with('notifications', $notifications);
    }
}

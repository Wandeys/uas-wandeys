<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $actionUrl = $notification->data['action_url'] ?? route('dashboard.index');
        return redirect($actionUrl);
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->withSuccess('Semua notifikasi ditandai sebagai telah dibaca.');
    }
}

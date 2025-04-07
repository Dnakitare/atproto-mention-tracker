<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationSettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the notification settings form.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $settings = Auth::user()->notificationSetting;
        return view('settings.notifications', compact('settings'));
    }

    /**
     * Update the notification settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'in_app_notifications' => 'boolean',
        ]);

        $settings = Auth::user()->notificationSetting;
        $settings->update([
            'email_notifications' => $request->boolean('email_notifications', false),
            'in_app_notifications' => $request->boolean('in_app_notifications', false),
        ]);

        return redirect()->route('settings.notifications')
            ->with('success', 'Notification settings updated successfully.');
    }
} 
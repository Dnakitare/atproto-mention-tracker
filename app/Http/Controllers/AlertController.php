<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AlertController extends Controller
{
    public function index(): View
    {
        $alerts = auth()->user()->alerts()->latest()->get();
        return view('alerts.index', compact('alerts'));
    }

    public function create(): View
    {
        return view('alerts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'conditions' => 'required|array',
            'notification_frequency' => 'required|in:immediate,hourly,daily,weekly',
            'notification_channels' => 'required|array',
            'notification_channels.*' => 'in:email,slack',
        ]);

        // Determine alert type based on conditions
        if (in_array('sentiment_positive', $validated['conditions']) || in_array('sentiment_negative', $validated['conditions'])) {
            $validated['type'] = 'sentiment_spike';
        } elseif (isset($validated['conditions']['time_window'])) {
            $validated['type'] = 'mention_spike';
        } else {
            $validated['type'] = 'keyword_match';
        }

        $alert = auth()->user()->alerts()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'conditions' => $validated['conditions'],
            'notification_frequency' => $validated['notification_frequency'],
            'notification_channels' => $validated['notification_channels'],
        ]);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert created successfully.');
    }

    public function edit(Alert $alert): View
    {
        $this->authorize('update', $alert);
        return view('alerts.edit', compact('alert'));
    }

    public function update(Request $request, Alert $alert): RedirectResponse
    {
        $this->authorize('update', $alert);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'conditions' => 'required|array',
            'notification_frequency' => 'required|in:immediate,hourly,daily,weekly',
            'is_active' => 'boolean',
            'notification_channels' => 'required|array',
            'notification_channels.*' => 'in:email,slack',
        ]);

        // Determine alert type based on conditions
        if (in_array('sentiment_positive', $validated['conditions']) || in_array('sentiment_negative', $validated['conditions'])) {
            $validated['type'] = 'sentiment_spike';
        } elseif (isset($validated['conditions']['time_window'])) {
            $validated['type'] = 'mention_spike';
        } else {
            $validated['type'] = 'keyword_match';
        }

        $alert->update($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert updated successfully.');
    }

    public function destroy(Alert $alert): RedirectResponse
    {
        $this->authorize('delete', $alert);
        
        $alert->delete();

        return redirect()->route('alerts.index')
            ->with('success', 'Alert deleted successfully.');
    }
} 
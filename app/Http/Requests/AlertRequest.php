<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:sentiment_spike,mention_spike,keyword_match'],
            'threshold' => ['required', 'numeric', 'min:0', 'max:1'],
            'notification_channels' => ['required', 'array'],
            'notification_channels.*' => ['string', 'in:email,slack'],
            'keywords' => ['required_if:type,keyword_match', 'array'],
            'keywords.*' => ['string', 'max:50'],
            'time_window' => ['required', 'integer', 'min:1', 'max:1440'], // in minutes
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'An alert name is required',
            'type.required' => 'An alert type is required',
            'type.in' => 'The alert type must be one of: sentiment spike, mention spike, or keyword match',
            'threshold.required' => 'A threshold value is required',
            'threshold.numeric' => 'The threshold must be a number',
            'threshold.min' => 'The threshold must be at least 0',
            'threshold.max' => 'The threshold must not exceed 1',
            'notification_channels.required' => 'At least one notification channel is required',
            'notification_channels.*.in' => 'Invalid notification channel. Must be either email or slack',
            'keywords.required_if' => 'Keywords are required for keyword match alerts',
            'keywords.*.max' => 'Each keyword must not exceed 50 characters',
            'time_window.required' => 'A time window is required',
            'time_window.integer' => 'The time window must be a whole number',
            'time_window.min' => 'The time window must be at least 1 minute',
            'time_window.max' => 'The time window must not exceed 1440 minutes (24 hours)',
        ];
    }
} 
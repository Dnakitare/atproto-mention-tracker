@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h1>Notification Settings</h1>
            <p class="lead">Configure how you want to be notified about new mentions</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.notifications.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h5>Email Notifications</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" {{ $settings->email_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">Receive email notifications</label>
                            </div>
                            <div class="form-text">You'll receive an email whenever a new mention is found.</div>
                        </div>

                        <div class="mb-4">
                            <h5>In-App Notifications</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="in_app_notifications" name="in_app_notifications" value="1" {{ $settings->in_app_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="in_app_notifications">Receive in-app notifications</label>
                            </div>
                            <div class="form-text">You'll see notifications in the app whenever a new mention is found.</div>
                        </div>

                        <div class="mb-4">
                            <h5>Slack Notifications</h5>
                            <div class="form-group">
                                <label for="slack_webhook_url" class="form-label">Slack Webhook URL</label>
                                <input type="url" class="form-control" id="slack_webhook_url" name="slack_webhook_url" value="{{ $settings->slack_webhook_url }}" placeholder="https://hooks.slack.com/services/...">
                                <div class="form-text">Enter your Slack webhook URL to receive notifications in your Slack workspace. <a href="https://api.slack.com/messaging/webhooks" target="_blank">Learn how to create a webhook</a></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notification Types</h5>
                </div>
                <div class="card-body">
                    <h6>Email Notifications</h6>
                    <p>Receive an email whenever a new mention is found. This is useful if you want to be notified even when you're not using the app.</p>
                    
                    <h6>In-App Notifications</h6>
                    <p>See notifications in the app whenever a new mention is found. This is useful if you're actively using the app and want to be notified in real-time.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
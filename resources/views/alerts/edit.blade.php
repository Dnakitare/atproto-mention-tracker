<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Alert') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('alerts.update', $alert) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Alert Name</label>
                            <input type="text" name="name" id="name" value="{{ $alert->name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $alert->description }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="notification_frequency" class="block text-sm font-medium text-gray-700">Notification Frequency</label>
                            <select name="notification_frequency" id="notification_frequency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="immediate" {{ $alert->notification_frequency === 'immediate' ? 'selected' : '' }}>Immediate</option>
                                <option value="hourly" {{ $alert->notification_frequency === 'hourly' ? 'selected' : '' }}>Hourly</option>
                                <option value="daily" {{ $alert->notification_frequency === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ $alert->notification_frequency === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Notification Channels</label>
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="notification_channel_email" name="notification_channels[]" value="email" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ in_array('email', $alert->notification_channels ?? []) ? 'checked' : '' }}>
                                    <label for="notification_channel_email" class="ml-2 block text-sm text-gray-700">Email</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="notification_channel_slack" name="notification_channels[]" value="slack" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ in_array('slack', $alert->notification_channels ?? []) ? 'checked' : '' }}>
                                    <label for="notification_channel_slack" class="ml-2 block text-sm text-gray-700">Slack</label>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Select the channels where you want to receive notifications. For Slack, make sure you've configured your webhook URL in your notification settings.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Conditions</label>
                            <div class="mt-2 space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="conditions[]" value="sentiment_positive" {{ in_array('sentiment_positive', $alert->conditions) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700">Positive Sentiment</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="conditions[]" value="sentiment_negative" {{ in_array('sentiment_negative', $alert->conditions) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700">Negative Sentiment</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="conditions[]" value="has_media" {{ in_array('has_media', $alert->conditions) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700">Contains Media</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="conditions[]" value="is_reply" {{ in_array('is_reply', $alert->conditions) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700">Is Reply</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $alert->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('alerts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Alert
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 
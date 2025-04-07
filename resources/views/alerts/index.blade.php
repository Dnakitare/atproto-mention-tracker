<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Alerts') }}
            </h2>
            <a href="{{ route('alerts.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Alert
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($alerts->isEmpty())
                        <p class="text-gray-500 text-center">No alerts created yet.</p>
                    @else
                        <div class="grid gap-4">
                            @foreach($alerts as $alert)
                                <div class="border rounded-lg p-4 {{ $alert->is_active ? 'bg-white' : 'bg-gray-50' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold">{{ $alert->name }}</h3>
                                            @if($alert->description)
                                                <p class="text-gray-600 mt-1">{{ $alert->description }}</p>
                                            @endif
                                            <div class="mt-2 text-sm text-gray-500">
                                                <span class="inline-block px-2 py-1 rounded {{ $alert->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $alert->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <span class="ml-2">Frequency: {{ ucfirst($alert->notification_frequency) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('alerts.edit', $alert) }}" class="text-blue-600 hover:text-blue-800">
                                                Edit
                                            </a>
                                            <form action="{{ route('alerts.destroy', $alert) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this alert?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 
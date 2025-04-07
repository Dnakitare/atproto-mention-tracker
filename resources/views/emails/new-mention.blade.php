@component('mail::message')
# New Mention on Bluesky

Hello {{ $user->name }},

You have a new mention on Bluesky that matches your tracked keyword "{{ $mention->keyword->keyword }}".

**Post by {{ $mention->author_handle }}:**
{{ $mention->text }}

@component('mail::button', ['url' => $mention->url])
View Post
@endcomponent

You can manage your tracked keywords and notification settings in your [dashboard]({{ route('dashboard') }}).

Thanks,<br>
{{ config('app.name') }}
@endcomponent 
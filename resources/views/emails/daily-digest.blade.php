@component('mail::message')
# Your Daily Bluesky Mentions Digest

Hello {{ $user->name }},

Here's a summary of your mentions on Bluesky for {{ $date }}:

@if($mentions->isEmpty())
You had no new mentions today.
@else
@foreach($mentions as $mention)
## Mention for "{{ $mention->keyword->keyword }}"
**Post by {{ $mention->author_handle }}:**
{{ $mention->text }}

@component('mail::button', ['url' => $mention->url])
View Post
@endcomponent

---
@endforeach
@endif

You can manage your tracked keywords and notification settings in your [dashboard]({{ route('dashboard') }}).

Thanks,<br>
{{ config('app.name') }}
@endcomponent 
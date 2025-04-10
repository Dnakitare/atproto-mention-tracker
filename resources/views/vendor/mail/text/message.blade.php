{{-- Greeting --}}
@if (! empty($greeting))
{{ $greeting }}
@else
@if ($level === 'error')
@lang('Whoops!')
@else
@lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
{{ $actionText }}: {{ $actionUrl }}
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>
{{ config('app.name') }}
@endif 
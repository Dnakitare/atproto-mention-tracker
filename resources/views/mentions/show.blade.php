@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ __('Mention Details') }}</h2>
                    <a href="{{ route('mentions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> {{ __('Back to Mentions') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ __('Keyword') }}</h5>
                            <p>
                                <span class="badge bg-primary">{{ $mention->keyword->keyword }}</span>
                                <span class="badge bg-{{ $mention->keyword->is_active ? 'success' : 'danger' }}">
                                    {{ $mention->keyword->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('Date') }}</h5>
                            <p>{{ $mention->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>{{ __('Author') }}</h5>
                            <p>
                                <a href="https://bsky.app/profile/{{ $mention->author_handle }}" target="_blank">
                                    {{ $mention->author_handle }}
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>{{ __('Post Text') }}</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $mention->text }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>{{ __('Actions') }}</h5>
                            <a href="{{ $mention->url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right"></i> {{ __('View on Bluesky') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
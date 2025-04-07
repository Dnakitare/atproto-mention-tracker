@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Dashboard') }}</h2>
                </div>
                <div class="card-body">
                    <p class="lead">{{ __('Welcome to Bluesky Mention Tracker. Track mentions of your keywords, usernames, and hashtags on Bluesky.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ __('Tracked Keywords') }}</h3>
                    <a href="{{ route('keywords.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> {{ __('Add Keyword') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($keywords->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('You have not added any keywords yet.') }}</p>
                            <a href="{{ route('keywords.create') }}" class="btn btn-primary mt-3">{{ __('Add Your First Keyword') }}</a>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($keywords as $keyword)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $keyword->keyword }}</span>
                                        <span class="badge bg-{{ $keyword->is_active ? 'success' : 'danger' }}">
                                            {{ $keyword->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('keywords.edit', $keyword) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('keywords.index') }}" class="btn btn-outline-secondary btn-sm">
                                {{ __('View All Keywords') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ __('Recent Mentions') }}</h3>
                    <a href="{{ route('mentions.index') }}" class="btn btn-outline-primary btn-sm">
                        {{ __('View All') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($mentions->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('No mentions found yet.') }}</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($mentions as $mention)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary me-2">{{ $mention->keyword->keyword }}</span>
                                            <small class="text-muted">{{ $mention->created_at->diffForHumans() }}</small>
                                        </div>
                                        <a href="{{ route('mentions.show', $mention) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                    <p class="mb-1 mt-2">{{ Str::limit($mention->text, 100) }}</p>
                                    <small>
                                        <a href="https://bsky.app/profile/{{ $mention->author_handle }}" target="_blank">
                                            {{ $mention->author_handle }}
                                        </a>
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
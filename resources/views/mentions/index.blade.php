@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Your Mentions') }}</h2>
                </div>
                <div class="card-body">
                    @if($mentions->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('You have no mentions yet. Add keywords to start tracking mentions.') }}</p>
                            <a href="{{ route('keywords.create') }}" class="btn btn-primary mt-3">{{ __('Add Keywords') }}</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Keyword') }}</th>
                                        <th>{{ __('Author') }}</th>
                                        <th>{{ __('Text') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mentions as $mention)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $mention->keyword->keyword }}</span>
                                            </td>
                                            <td>
                                                <a href="https://bsky.app/profile/{{ $mention->author_handle }}" target="_blank">
                                                    {{ $mention->author_handle }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ Str::limit($mention->text, 100) }}
                                            </td>
                                            <td>
                                                {{ $mention->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td>
                                                <a href="{{ $mention->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-box-arrow-up-right"></i> {{ __('View') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $mentions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
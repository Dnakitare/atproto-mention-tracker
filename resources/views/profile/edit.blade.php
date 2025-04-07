@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Profile Information') }}</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Update Profile') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{ __('Account Information') }}</h3>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Member since:') }}</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    <p><strong>{{ __('Last login:') }}</strong> {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : __('Never') }}</p>
                    
                    <div class="mt-4">
                        <h4>{{ __('Account Actions') }}</h4>
                        <div class="d-grid gap-2">
                            <a href="{{ route('password.request') }}" class="btn btn-outline-primary">
                                {{ __('Change Password') }}
                            </a>
                            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('{{ __('Are you sure you want to delete your account? This action cannot be undone.') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    {{ __('Delete Account') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
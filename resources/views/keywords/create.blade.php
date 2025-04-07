@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h1>Add Keyword</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('keywords.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="keyword" class="form-label">Keyword</label>
                            <input type="text" class="form-control @error('keyword') is-invalid @enderror" id="keyword" name="keyword" value="{{ old('keyword') }}" required>
                            <div class="form-text">Enter the keyword, username, or hashtag you want to track.</div>
                            @error('keyword')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="keyword" {{ old('type') === 'keyword' ? 'selected' : '' }}>Keyword</option>
                                <option value="username" {{ old('type') === 'username' ? 'selected' : '' }}>Username</option>
                                <option value="hashtag" {{ old('type') === 'hashtag' ? 'selected' : '' }}>Hashtag</option>
                            </select>
                            <div class="form-text">Select the type of keyword you want to track.</div>
                            @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Uncheck to temporarily disable tracking for this keyword.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('keywords.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Keyword</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Keyword Types</h5>
                </div>
                <div class="card-body">
                    <h6>Keyword</h6>
                    <p>Track any text or phrase in posts.</p>
                    
                    <h6>Username</h6>
                    <p>Track mentions of a specific Bluesky username.</p>
                    
                    <h6>Hashtag</h6>
                    <p>Track posts containing a specific hashtag.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
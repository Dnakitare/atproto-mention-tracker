@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Tracked Keywords</h1>
                <a href="{{ route('keywords.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Keyword
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($keywords->isEmpty())
                        <p>You haven't added any keywords to track yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Keyword</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($keywords as $keyword)
                                        <tr>
                                            <td>
                                                @if($keyword->type === 'username')
                                                    @{{ $keyword->keyword }}
                                                @elseif($keyword->type === 'hashtag')
                                                    #{{ $keyword->keyword }}
                                                @else
                                                    {{ $keyword->keyword }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst($keyword->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $keyword->is_active ? 'success' : 'secondary' }}">
                                                    {{ $keyword->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $keyword->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('keywords.edit', $keyword->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('keywords.destroy', $keyword->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this keyword?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
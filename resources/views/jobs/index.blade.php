@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3">
            <!-- Filters Sidebar -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Filter Jobs</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.index') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" placeholder="Keywords...">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Budget Range -->
                        <div class="mb-3">
                            <label class="form-label">Budget Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="budget_min" class="form-control" 
                                           placeholder="Min" value="{{ request('budget_min') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="budget_max" class="form-control" 
                                           placeholder="Max" value="{{ request('budget_max') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Experience Level -->
                        <div class="mb-3">
                            <label class="form-label">Experience Level</label>
                            <select name="experience_level" class="form-select">
                                <option value="">Any Level</option>
                                <option value="beginner" {{ request('experience_level') == 'beginner' ? 'selected' : '' }}>
                                    Beginner
                                </option>
                                <option value="intermediate" {{ request('experience_level') == 'intermediate' ? 'selected' : '' }}>
                                    Intermediate
                                </option>
                                <option value="expert" {{ request('experience_level') == 'expert' ? 'selected' : '' }}>
                                    Expert
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Find Jobs</h2>
                @auth
                    @if(auth()->user()->isClient())
                        <a href="{{ route('jobs.create') }}" class="btn btn-primary">Post a Job</a>
                    @endif
                @endauth
            </div>

            @if($jobs->count() > 0)
                <div class="row">
                    @foreach($jobs as $job)
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="card-title">
                                                <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                                                    {{ $job->title }}
                                                </a>
                                            </h5>
                                            <p class="card-text text-muted">{{ Str::limit($job->description, 150) }}</p>
                                            
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                @if($job->skills_required)
                                                    @foreach(array_slice($job->skills_required, 0, 3) as $skill)
                                                        <span class="badge bg-light text-dark">{{ $skill }}</span>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> {{ $job->user->location ?? 'Remote' }} •
                                                <i class="bi bi-clock"></i> {{ $job->created_at->diffForHumans() }} •
                                                <i class="bi bi-person"></i> {{ $job->proposals_count ?? 0 }} proposals
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="mb-2">
                                                @if($job->budget_type === 'fixed')
                                                    <h6 class="text-success mb-0">${{ number_format($job->fixed_budget, 2) }}</h6>
                                                    <small class="text-muted">Fixed Price</small>
                                                @else
                                                    <h6 class="text-success mb-0">
                                                        ${{ number_format($job->budget_min, 2) }} - ${{ number_format($job->budget_max, 2) }}
                                                    </h6>
                                                    <small class="text-muted">Hourly</small>
                                                @endif
                                            </div>
                                            
                                            <span class="badge bg-primary">{{ ucfirst($job->experience_level) }}</span>
                                            
                                            @if($job->featured)
                                                <span class="badge bg-warning text-dark ms-1">Featured</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $jobs->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <h4 class="mt-3">No jobs found</h4>
                    <p class="text-muted">Try adjusting your search criteria or check back later for new opportunities.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3">{{ $job->title }}</h1>
                            <div class="text-muted mb-2">
                                <i class="bi bi-geo-alt"></i> {{ $job->user->location ?? 'Remote' }} •
                                <i class="bi bi-clock"></i> Posted {{ $job->created_at->diffForHumans() }}
                            </div>
                        </div>
                        @if($job->featured)
                            <span class="badge bg-warning text-dark">Featured</span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h5>Job Description</h5>
                        <div class="job-description">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>

                    @if($job->skills_required)
                        <div class="mb-4">
                            <h6>Skills Required</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($job->skills_required as $skill)
                                    <span class="badge bg-light text-dark">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Budget</h6>
                            @if($job->budget_type === 'fixed')
                                <p class="text-success fs-5 mb-0">${{ number_format($job->fixed_budget, 2) }}</p>
                                <small class="text-muted">Fixed Price</small>
                            @else
                                <p class="text-success fs-5 mb-0">
                                    ${{ number_format($job->budget_min, 2) }} - ${{ number_format($job->budget_max, 2) }}
                                </p>
                                <small class="text-muted">Hourly Rate</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Experience Level</h6>
                            <span class="badge bg-primary">{{ ucfirst($job->experience_level) }}</span>
                        </div>
                    </div>

                    @if($job->deadline)
                        <div class="mb-4">
                            <h6>Deadline</h6>
                            <p>{{ $job->deadline->format('M d, Y') }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h6>Category</h6>
                        <span class="badge bg-secondary">{{ $job->category->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Proposals Section -->
            @if(auth()->check() && auth()->user()->id === $job->user_id)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Proposals ({{ $job->proposals->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @forelse($job->proposals as $proposal)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <img src="{{ $proposal->freelancer->avatar ?? '/placeholder.svg?height=50&width=50' }}" 
                                             alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                                        <div>
                                            <h6 class="mb-1">{{ $proposal->freelancer->name }}</h6>
                                            <div class="text-muted small mb-2">
                                                @if($proposal->freelancer->freelancerProfile)
                                                    <span class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="bi bi-star{{ $proposal->freelancer->freelancerProfile->average_rating >= $i ? '-fill' : '' }}"></i>
                                                        @endfor
                                                    </span>
                                                    ({{ $proposal->freelancer->freelancerProfile->total_reviews }} reviews)
                                                @endif
                                            </div>
                                            <p class="mb-2">{{ Str::limit($proposal->cover_letter, 200) }}</p>
                                            <small class="text-muted">
                                                Budget: ${{ number_format($proposal->proposed_budget, 2) }} • 
                                                Delivery: {{ $proposal->delivery_time }} days
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @if($proposal->status === 'pending')
                                            <form method="POST" action="{{ route('proposals.accept', $proposal) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('proposals.reject', $proposal) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                            </form>
                                        @else
                                            <span class="badge bg-{{ $proposal->status === 'accepted' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($proposal->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No proposals yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Client Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">About the Client</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $job->user->avatar ?? '/placeholder.svg?height=60&width=60' }}" 
                             alt="Client Avatar" class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h6 class="mb-1">{{ $job->user->name }}</h6>
                            <small class="text-muted">Member since {{ $job->user->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="mb-0">{{ $job->user->jobs()->count() }}</h6>
                                <small class="text-muted">Jobs Posted</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h6 class="mb-0">
                                    @if($job->user->givenReviews()->count() > 0)
                                        {{ number_format($job->user->givenReviews()->avg('rating'), 1) }}
                                    @else
                                        N/A
                                    @endif
                                </h6>
                                <small class="text-muted">Avg Rating</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0">{{ $job->user->givenReviews()->count() }}</h6>
                            <small class="text-muted">Reviews</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Proposal -->
            @auth
                @if(auth()->user()->isFreelancer() && auth()->user()->id !== $job->user_id)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Submit a Proposal</h6>
                        </div>
                        <div class="card-body">
                            @if($userProposal)
                                <div class="alert alert-info">
                                    <h6>Your Proposal Status: 
                                        <span class="badge bg-{{ $userProposal->status === 'accepted' ? 'success' : ($userProposal->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($userProposal->status) }}
                                        </span>
                                    </h6>
                                    <p class="mb-2"><strong>Budget:</strong> ${{ number_format($userProposal->proposed_budget, 2) }}</p>
                                    <p class="mb-2"><strong>Delivery:</strong> {{ $userProposal->delivery_time }} days</p>
                                    
                                    @if($userProposal->status === 'pending')
                                        <form method="POST" action="{{ route('proposals.withdraw', $userProposal) }}" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Withdraw Proposal</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <form method="POST" action="{{ route('proposals.store', $job) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Cover Letter</label>
                                        <textarea name="cover_letter" class="form-control" rows="4" 
                                                  placeholder="Describe your approach to this project..." required></textarea>
                                        @error('cover_letter')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Your Budget ($)</label>
                                        <input type="number" name="proposed_budget" class="form-control" 
                                               step="0.01" min="1" required>
                                        @error('proposed_budget')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Delivery Time (days)</label>
                                        <input type="number" name="delivery_time" class="form-control" 
                                               min="1" required>
                                        @error('delivery_time')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Attachments (optional)</label>
                                        <input type="file" name="attachments[]" class="form-control" multiple>
                                        <small class="text-muted">Max 10MB per file</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Submit Proposal</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Want to submit a proposal?</h6>
                        <p class="text-muted">Sign up as a freelancer to apply for this job.</p>
                        <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection

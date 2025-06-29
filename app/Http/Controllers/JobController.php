<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Category;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['user', 'category', 'proposals'])
            ->open()
            ->latest();

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Budget filter
        if ($request->filled('budget_min')) {
            $query->where('budget_min', '>=', $request->budget_min);
        }

        if ($request->filled('budget_max')) {
            $query->where('budget_max', '<=', $request->budget_max);
        }

        // Experience level filter
        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }

        $jobs = $query->paginate(12);
        $categories = Category::active()->parent()->get();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    public function show(Job $job)
    {
        $job->load(['user', 'category', 'proposals.freelancer.freelancerProfile']);
        
        $userProposal = null;
        if (Auth::check() && Auth::user()->isFreelancer()) {
            $userProposal = $job->proposals()->where('user_id', Auth::id())->first();
        }

        return view('jobs.show', compact('job', 'userProposal'));
    }

    public function create()
    {
        $this->authorize('create', Job::class);
        
        $categories = Category::active()->parent()->with('children')->get();
        
        return view('jobs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'budget_type' => 'required|in:hourly,fixed',
            'budget_min' => 'required_if:budget_type,hourly|nullable|numeric|min:0',
            'budget_max' => 'required_if:budget_type,hourly|nullable|numeric|min:0',
            'fixed_budget' => 'required_if:budget_type,fixed|nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'skills_required' => 'nullable|array',
            'experience_level' => 'required|in:beginner,intermediate,expert',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = Job::STATUS_OPEN;

        $job = Job::create($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job posted successfully!');
    }

    public function edit(Job $job)
    {
        $this->authorize('update', $job);
        
        $categories = Category::active()->parent()->with('children')->get();
        
        return view('jobs.edit', compact('job', 'categories'));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'budget_type' => 'required|in:hourly,fixed',
            'budget_min' => 'required_if:budget_type,hourly|nullable|numeric|min:0',
            'budget_max' => 'required_if:budget_type,hourly|nullable|numeric|min:0',
            'fixed_budget' => 'required_if:budget_type,fixed|nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'skills_required' => 'nullable|array',
            'experience_level' => 'required|in:beginner,intermediate,expert',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job updated successfully!');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return redirect()->route('jobs.index')
            ->with('success', 'Job deleted successfully!');
    }

    public function myJobs()
    {
        $jobs = Auth::user()->jobs()->with(['category', 'proposals'])->latest()->paginate(10);
        
        return view('jobs.my-jobs', compact('jobs'));
    }
}

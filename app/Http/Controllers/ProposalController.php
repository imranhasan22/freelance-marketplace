<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $this->authorize('create', [Proposal::class, $job]);

        $validated = $request->validate([
            'cover_letter' => 'required|string|min:50',
            'proposed_budget' => 'required|numeric|min:1',
            'delivery_time' => 'required|integer|min:1',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('proposals', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $proposal = Proposal::create([
            'job_id' => $job->id,
            'user_id' => Auth::id(),
            'cover_letter' => $validated['cover_letter'],
            'proposed_budget' => $validated['proposed_budget'],
            'delivery_time' => $validated['delivery_time'],
            'attachments' => $attachments,
            'status' => Proposal::STATUS_PENDING,
        ]);

        // Send notification to job owner
        $job->user->notify(new \App\Notifications\NewProposalReceived($proposal));

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Proposal submitted successfully!');
    }

    public function show(Proposal $proposal)
    {
        $this->authorize('view', $proposal);
        
        $proposal->load(['job', 'freelancer.freelancerProfile']);
        
        return view('proposals.show', compact('proposal'));
    }

    public function accept(Proposal $proposal)
    {
        $this->authorize('accept', $proposal);

        // Reject all other proposals for this job
        $proposal->job->proposals()
            ->where('id', '!=', $proposal->id)
            ->update(['status' => Proposal::STATUS_REJECTED]);

        // Accept this proposal
        $proposal->update(['status' => Proposal::STATUS_ACCEPTED]);

        // Update job status
        $proposal->job->update(['status' => Job::STATUS_IN_PROGRESS]);

        // Send notifications
        $proposal->freelancer->notify(new \App\Notifications\ProposalAccepted($proposal));

        return redirect()->route('jobs.show', $proposal->job)
            ->with('success', 'Proposal accepted successfully!');
    }

    public function reject(Proposal $proposal)
    {
        $this->authorize('reject', $proposal);

        $proposal->update(['status' => Proposal::STATUS_REJECTED]);

        // Send notification
        $proposal->freelancer->notify(new \App\Notifications\ProposalRejected($proposal));

        return redirect()->route('jobs.show', $proposal->job)
            ->with('success', 'Proposal rejected.');
    }

    public function withdraw(Proposal $proposal)
    {
        $this->authorize('withdraw', $proposal);

        $proposal->update(['status' => Proposal::STATUS_WITHDRAWN]);

        return redirect()->route('jobs.show', $proposal->job)
            ->with('success', 'Proposal withdrawn successfully.');
    }

    public function myProposals()
    {
        $proposals = Auth::user()->proposals()
            ->with(['job.user'])
            ->latest()
            ->paginate(10);

        return view('proposals.my-proposals', compact('proposals'));
    }
}

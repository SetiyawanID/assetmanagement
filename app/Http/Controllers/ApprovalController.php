<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(): View
    {
        $user = request()->user();
        $query = ApprovalRequest::with(['requester', 'reviewer'])->latest();
        if (! $user->isSuperAdmin()) {
            $query->where('requested_by', $user->id);
        }
        $approvals = $query->get();

        if ($user->isAdmin()) {
            ApprovalRequest::where('requested_by', $user->id)
                ->whereNotNull('reviewed_at')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('approvals.index', [
            'approvals' => $approvals,
        ]);
    }

    public function approve(Request $request, ApprovalRequest $approval): RedirectResponse
    {
        abort_unless($approval->status === 'pending', 404);
        $payload = $approval->payload;

        DB::transaction(function () use ($request, $approval, $payload): void {
            if ($approval->type === 'category') {
                Category::create($payload);
            } elseif ($approval->type === 'status') {
                Status::create($payload);
            } else {
                DB::table('users')->insert([
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'role' => 'user',
                    'division_id' => $payload['division_id'] ?? null,
                    'password' => $payload['password_hash'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $approval->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', $approval->type_label.' berhasil di-approve.');
    }

    public function reject(Request $request, ApprovalRequest $approval): RedirectResponse
    {
        abort_unless($approval->status === 'pending', 404);
        $approval->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        return back()->with('success', 'Pengajuan '.$approval->type_label.' ditolak.');
    }
}

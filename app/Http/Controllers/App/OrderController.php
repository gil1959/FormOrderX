<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Submission::query()
            ->with('form')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('submitted_at');

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        if ($request->filled('payment_status')) {
            $q->where('payment_status', $request->string('payment_status'));
        }

        $orders = $q->paginate(20)->withQueryString();

        return view('app.orders.index', compact('orders'));
    }
    public function destroy(Request $request, Submission $submission)
    {
        abort_unless($submission->user_id === $request->user()->id, 403);

        $submission->delete();

        return back()->with('success', 'Order berhasil dihapus.');
    }


    public function updateStatus(Request $request, Submission $submission)
    {
        abort_unless($submission->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'action' => ['required', 'string'],
        ]);

        $action = $data['action'];

        // mapping action dropdown -> kolom
        switch ($action) {
            case 'mark_pending':
                $submission->status = 'pending';
                break;
            case 'mark_processed':
                $submission->status = 'processed';
                break;
            case 'mark_completed':
                $submission->status = 'completed';
                break;
            case 'mark_cancelled':
                $submission->status = 'cancelled';
                break;
            case 'mark_paid':
                $submission->payment_status = 'paid';
                break;
            case 'mark_unpaid':
                $submission->payment_status = 'unpaid';
                break;
            case 'mark_refunded':
                $submission->payment_status = 'refunded';
                $submission->status = 'cancelled'; // biar konsisten
                break;
            default:
                return back()->with('error', 'Aksi tidak dikenal.');
        }

        $submission->save();

        return back()->with('success', 'Status berhasil diubah.');
    }
}

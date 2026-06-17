<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __invoke(Request $request): View
    {
        $subscriptions = Subscription::query()
            ->withoutWorkspaceScope()
            ->with(['workspace', 'plan'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('platform.subscriptions.index', ['subscriptions' => $subscriptions]);
    }
}

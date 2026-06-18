<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Support\CurrentWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanySettingsController extends Controller
{
    public function edit(CurrentWorkspace $currentWorkspace): View
    {
        return view('tenant.settings.company', [
            'workspace' => $currentWorkspace->required()->loadMissing('settings'),
        ]);
    }

    public function update(Request $request, CurrentWorkspace $currentWorkspace): RedirectResponse
    {
        $workspace = $currentWorkspace->required();
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'timezone' => ['required', 'string', 'max:100'],
            'currency' => ['required', 'string', 'size:3'],
            'logo' => ['nullable', 'file', 'image', 'max:2048'],
            'ai_enabled' => ['nullable', 'boolean'],
            'ai_preferred_provider' => ['nullable', 'in:disabled,local,cloud,hybrid'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('workspace-logos');
        }

        unset($validated['logo']);
        $flags = $workspace->settings?->feature_flags ?? [];
        $flags['ai_preferred_provider'] = $validated['ai_preferred_provider'] ?? config('ai.default_provider');
        unset($validated['ai_preferred_provider']);
        $validated['ai_enabled'] = (bool) ($validated['ai_enabled'] ?? false);
        $validated['feature_flags'] = $flags;
        $workspace->settings()->updateOrCreate(['workspace_id' => $workspace->id], $validated);

        return back()->with('status', 'Company settings updated.');
    }
}

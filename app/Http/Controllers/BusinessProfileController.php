<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Models\BusinessCategory;
use App\Services\BusinessProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BusinessProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $business = $request->user()->business;

        return Inertia::render('BusinessProfile/Edit', [
            'business' => $business,
            'categories' => BusinessCategory::query()->orderBy('name')->get(['id', 'name']),
            'identityLocked' => $business !== null && DB::table('verification_requests')
                ->where('business_id', $business->id)->where('status', 'approved')->exists(),
        ]);
    }

    public function update(UpdateBusinessProfileRequest $request, BusinessProfileService $service): RedirectResponse
    {
        $service->save($request->user(), $request->validated());

        return redirect()->route('business-profile.edit');
    }
}

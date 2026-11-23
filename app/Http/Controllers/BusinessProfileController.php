<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Services\BusinessProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BusinessProfileController extends Controller
{
    public function show(Request $request, Business $business): JsonResponse
    {
        return response()->json($business->user_id === $request->user()->id ? $business : $business->only([
            'id', 'business_category_id', 'name', 'description', 'address', 'district',
            'subdistrict', 'contact_phone', 'logo_path', 'business_needs',
        ]));
    }

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

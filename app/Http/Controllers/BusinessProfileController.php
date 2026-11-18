<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Models\BusinessCategory;
use App\Services\BusinessProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BusinessProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('BusinessProfile/Edit', [
            'business' => $request->user()->business,
            'categories' => BusinessCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateBusinessProfileRequest $request, BusinessProfileService $service): RedirectResponse
    {
        $service->save($request->user(), $request->validated());

        return redirect()->route('business-profile.edit');
    }
}

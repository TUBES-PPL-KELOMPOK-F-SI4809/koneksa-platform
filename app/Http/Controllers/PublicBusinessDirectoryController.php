<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicBusinessDirectoryController extends Controller
{
    /**
     * Tampilkan direktori publik UMKM terpublikasi beserta filter nama & kategori (FR-10).
     */
    public function index(Request $request): Response|JsonResponse
    {
        $search = trim((string) $request->input('search', ''));
        $categoryId = $request->input('category_id');

        $query = Business::query()
            ->published()
            ->with(['category', 'user'])
            ->with(['verificationRequests' => fn ($q) => $q->where('status', 'approved')->latest('reviewed_at')])
            ->latest('published_at');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($categoryId)) {
            $query->category($categoryId);
        }

        $businesses = $query->paginate(12)->withQueryString();

        $formattedBusinesses = $businesses->through(function (Business $b) {
            $isVerified = $b->verificationRequests->isNotEmpty();

            return [
                'id' => $b->id,
                'name' => $b->name,
                'description' => $b->description,
                'address' => $b->address,
                'district' => $b->district,
                'subdistrict' => $b->subdistrict,
                'contact_phone' => $b->contact_phone,
                'logo_path' => $b->logo_path,
                'business_category_id' => $b->business_category_id,
                'category_name' => $b->category?->name ?? 'Umum',
                'business_needs' => $b->business_needs,
                'is_verified' => $isVerified,
                'published_at' => $b->published_at?->toIso8601String(),
                'published_date_human' => $b->published_at?->translatedFormat('d M Y') ?? $b->published_at?->format('d M Y'),
            ];
        });

        $categories = BusinessCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $payload = [
            'businesses' => $formattedBusinesses,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category_id' => $categoryId ? (int) $categoryId : null,
            ],
            'total_count' => $businesses->total(),
        ];

        if ($request->wantsJson()) {
            return response()->json($payload);
        }

        return Inertia::render('Public/BusinessDirectory', $payload);
    }
}

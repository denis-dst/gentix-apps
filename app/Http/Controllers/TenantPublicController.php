<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MembershipTier;
use App\Models\SeasonPass;
use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TenantPublicController extends Controller
{
    /**
     * Display the public event listing for a specific tenant/organizer.
     *
     * Example URL: /{tenant:slug}/list-event/ (e.g. /lampung-youth/list-event/)
     */
    public function listEvents(Request $request, string $slug)
    {
        // 1. Resolve tenant by slug or prefix/name fallback
        $tenant = Tenant::where('slug', $slug)
            ->orWhere('slug', 'like', $slug . '-%')
            ->first();

        if (!$tenant) {
            $tenant = Tenant::where('name', 'like', '%' . $slug . '%')->first();
        }

        if (!$tenant || $tenant->status === 'deleted') {
            abort(404, 'Penyelenggara tidak ditemukan atau telah dinonaktifkan.');
        }

        // 2. Extract filter, sort, and search params
        $search = trim((string) $request->input('q', ''));
        $filter = $request->input('filter', 'upcoming'); // 'upcoming', 'past', 'all'
        $sort   = $request->input('sort', 'date_asc');   // 'date_asc', 'date_desc', 'name_asc'

        $now = now();

        // 3. Base published events query for this tenant
        $baseQuery = Event::with(['ticketCategories' => function ($q) {
            $q->where('is_active', true)->orderBy('price', 'asc');
        }])
        ->where('tenant_id', $tenant->id)
        ->where('status', 'published');

        // 4. Calculate tab counts before text search
        $upcomingCount = (clone $baseQuery)->where(function ($q) use ($now) {
            $q->where('event_end_date', '>=', $now)
              ->orWhere(function ($sub) use ($now) {
                  $sub->whereNull('event_end_date')
                      ->where('event_start_date', '>=', $now->copy()->subHours(6));
              });
        })->count();

        $pastCount = (clone $baseQuery)->where(function ($q) use ($now) {
            $q->where('event_end_date', '<', $now)
              ->orWhere(function ($sub) use ($now) {
                  $sub->whereNull('event_end_date')
                      ->where('event_start_date', '<', $now->copy()->subHours(6));
              });
        })->count();

        $totalCount = (clone $baseQuery)->count();

        // 5. Apply search filter
        $query = clone $baseQuery;
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('home_team_name', 'like', "%{$search}%")
                  ->orWhere('away_team_name', 'like', "%{$search}%");
            });
        }

        // 6. Apply time filter
        if ($filter === 'upcoming') {
            $query->where(function ($q) use ($now) {
                $q->where('event_end_date', '>=', $now)
                  ->orWhere(function ($sub) use ($now) {
                      $sub->whereNull('event_end_date')
                          ->where('event_start_date', '>=', $now->copy()->subHours(6));
                  });
            });
        } elseif ($filter === 'past') {
            $query->where(function ($q) use ($now) {
                $q->where('event_end_date', '<', $now)
                  ->orWhere(function ($sub) use ($now) {
                      $sub->whereNull('event_end_date')
                          ->where('event_start_date', '<', $now->copy()->subHours(6));
                  });
            });
        }

        // 7. Apply sorting
        if ($sort === 'date_desc') {
            $query->orderBy('event_start_date', 'desc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } else {
            $query->orderBy('event_start_date', 'asc');
        }

        $events = $query->paginate(9)->withQueryString();

        // 8. Check for Fan Membership & Season Pass availability for this tenant
        $hasMembership = MembershipTier::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->exists();

        $hasSeasonPass = SeasonPass::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->exists();

        // 9. Fetch global settings
        $settings = Cache::remember('public_settings_map', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        return view('public.tenant-events', compact(
            'tenant',
            'events',
            'filter',
            'sort',
            'search',
            'upcomingCount',
            'pastCount',
            'totalCount',
            'hasMembership',
            'hasSeasonPass',
            'settings'
        ));
    }
}

<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketCategoryController extends Controller
{
    public function create(Event $event)
    {
        $this->authorizeTenant($event);
        return view('organizer.tickets.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorizeTenant($event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'nik_restriction' => 'nullable|string|max:255',
            'nik_restriction_message' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'quota' => 'required|integer|min:1',
            'sale_start_at' => 'nullable|date',
            'sale_end_at' => 'nullable|date|after:sale_start_at',
            'hex_color' => 'nullable|string|size:7',
            'category_image' => 'nullable|image|max:1024',
            'background_image' => 'nullable|image|max:2048',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
        ]);

        $data = $validated;
        $data['event_id'] = $event->id;
        $data['tenant_id'] = $event->tenant_id;
        $data['layout_config'] = $this->buildWristbandLayoutConfig($request);
        unset(
            $data['wristband_league_name'],
            $data['wristband_league_logo'],
            $data['wristband_home_club_logo'],
            $data['wristband_away_club_logo'],
            $data['wristband_sponsor_logos']
        );

        if ($request->hasFile('category_image')) {
            $data['category_image'] = $request->file('category_image')->store('tickets/categories', 'public');
        }

        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('tickets/backgrounds', 'public');
        }

        TicketCategory::create($data);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Ticket category created.');
    }

    public function edit(Event $event, TicketCategory $category)
    {
        $this->authorizeTenant($event);
        if ($category->event_id != $event->id) abort(403);

        return view('organizer.tickets.edit', compact('event', 'category'));
    }

    public function update(Request $request, Event $event, TicketCategory $category)
    {
        $this->authorizeTenant($event);
        if ($category->event_id !== $event->id) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'nik_restriction' => 'nullable|string|max:255',
            'nik_restriction_message' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'quota' => 'required|integer|min:1',
            'sale_start_at' => 'nullable|date',
            'sale_end_at' => 'nullable|date|after:sale_start_at',
            'hex_color' => 'nullable|string|size:7',
            'category_image' => 'nullable|image|max:1024',
            'background_image' => 'nullable|image|max:2048',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
        ]);

        $data = $validated;
        $data['layout_config'] = $this->buildWristbandLayoutConfig($request, $category->layout_config ?? []);
        unset(
            $data['wristband_league_name'],
            $data['wristband_league_logo'],
            $data['wristband_home_club_logo'],
            $data['wristband_away_club_logo'],
            $data['wristband_sponsor_logos']
        );

        if ($request->hasFile('category_image')) {
            if ($category->category_image) Storage::disk('public')->delete($category->category_image);
            $data['category_image'] = $request->file('category_image')->store('tickets/categories', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($category->background_image) Storage::disk('public')->delete($category->background_image);
            $data['background_image'] = $request->file('background_image')->store('tickets/backgrounds', 'public');
        }

        $category->update($data);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Ticket category updated.');
    }

    public function destroy(Event $event, TicketCategory $category)
    {
        $this->authorizeTenant($event);
        if ($category->event_id !== $event->id) abort(403);

        $category->delete();
        return redirect()->route('organizer.events.edit', $event)->with('success', 'Ticket category deleted.');
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id != auth()->user()->tenant_id && !auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Unauthorized access to this event');
        }
    }

    private function buildWristbandLayoutConfig(Request $request, array $current = []): array
    {
        $config = $current;

        $config['league_name'] = $request->input('wristband_league_name') ?: ($config['league_name'] ?? null);

        foreach ([
            'wristband_league_logo' => 'league_logo',
            'wristband_home_club_logo' => 'home_club_logo',
            'wristband_away_club_logo' => 'away_club_logo',
        ] as $input => $key) {
            if ($request->hasFile($input)) {
                if (!empty($config[$key])) {
                    Storage::disk('public')->delete($config[$key]);
                }
                $config[$key] = $request->file($input)->store('wristbands/logos', 'public');
            }
        }

        if ($request->hasFile('wristband_sponsor_logos')) {
            foreach (($config['sponsor_logos'] ?? []) as $logo) {
                Storage::disk('public')->delete($logo);
            }

            $config['sponsor_logos'] = collect($request->file('wristband_sponsor_logos'))
                ->filter()
                ->map(fn ($file) => $file->store('wristbands/sponsors', 'public'))
                ->values()
                ->all();
        }

        return array_filter($config, fn ($value) => filled($value));
    }
}

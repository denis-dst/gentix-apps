<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\MatchPrediction;
use App\Models\FanQuiz;
use App\Models\FanPointsLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    private function getTenantId()
    {
        return Auth::user()->tenant_id;
    }

    public function index()
    {
        $tenantId = $this->getTenantId();

        $events = Event::where('tenant_id', $tenantId)
            ->whereNotNull('home_team_name')
            ->latest('event_start_date')
            ->take(15)
            ->get();

        $quizzes = FanQuiz::where('tenant_id', $tenantId)
            ->withCount('participants')
            ->latest()
            ->get();

        $predictions = MatchPrediction::where('tenant_id', $tenantId)
            ->with(['event', 'member.user'])
            ->latest()
            ->paginate(20);

        return view('organizer.gamification.index', compact('events', 'quizzes', 'predictions'));
    }

    public function updateMatchScore(Request $request, Event $event)
    {
        if ($event->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'reward_points' => 'required|integer|min:10',
        ]);

        $event->update([
            'home_score' => $validated['home_score'],
            'away_score' => $validated['away_score'],
        ]);

        // Hitung semua tebakan yang tepat dan berikan poin
        $predictions = MatchPrediction::where('event_id', $event->id)->where('is_calculated', false)->get();
        $rewardPoints = $validated['reward_points'];
        $correctCount = 0;

        foreach ($predictions as $prediction) {
            $isCorrect = ($prediction->predicted_home_score == $validated['home_score']) &&
                         ($prediction->predicted_away_score == $validated['away_score']);

            $prediction->update([
                'is_calculated' => true,
                'is_correct' => $isCorrect,
                'points_rewarded' => $isCorrect ? $rewardPoints : 0,
            ]);

            if ($isCorrect) {
                $correctCount++;
                $member = $prediction->member;
                if ($member) {
                    $member->increment('points_balance', $rewardPoints);
                    FanPointsLedger::create([
                        'tenant_id' => $event->tenant_id,
                        'tenant_member_id' => $member->id,
                        'type' => 'earn',
                        'source' => 'score_prediction',
                        'points' => $rewardPoints,
                        'balance_after' => $member->points_balance,
                        'description' => "Hadiah tebak skor pertandingan {$event->home_team_name} vs {$event->away_team_name} ({$validated['home_score']}-{$validated['away_score']})",
                    ]);
                }
            }
        }

        return back()->with('success', "Skor pertandingan diperbarui! {$correctCount} suporter berhasil menebak dengan benar dan mendapatkan {$rewardPoints} poin.");
    }

    public function storeQuiz(Request $request)
    {
        $tenantId = $this->getTenantId();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'points_reward' => 'required|integer|min:10',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correct_index' => 'required|integer',
        ]);

        FanQuiz::create([
            'tenant_id' => $tenantId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $validated['starts_at'],
            'expires_at' => $validated['expires_at'],
            'points_reward' => $validated['points_reward'],
            'questions' => $validated['questions'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Kuis Klub berhasil dipublikasikan!');
    }
}

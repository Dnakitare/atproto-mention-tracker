<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Mention;
use App\Models\TrackedKeyword;
use App\Services\MentionTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $mentionTrackingService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MentionTrackingService $mentionTrackingService)
    {
        $this->middleware('auth');
        $this->mentionTrackingService = $mentionTrackingService;
    }

    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's keywords
        $keywords = Keyword::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get user's recent mentions
        $mentions = Mention::whereHas('keyword', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('keyword')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact('keywords', 'mentions'));
    }

    /**
     * Show mentions for a specific keyword.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $keyword
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function keywordMentions(Request $request, $keyword)
    {
        $user = Auth::user();
        $mentions = $this->mentionTrackingService->getMentionsByKeyword($user, $keyword);
        $keywords = $user->trackedKeywords()->get();

        return view('dashboard', compact('mentions', 'keywords', 'keyword'));
    }
} 
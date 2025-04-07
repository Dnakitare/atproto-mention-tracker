<?php

namespace App\Http\Controllers;

use App\Models\Mention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mentions = Mention::whereHas('keyword', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with('keyword')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('mentions.index', compact('mentions'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mention  $mention
     * @return \Illuminate\Http\Response
     */
    public function show(Mention $mention)
    {
        // Check if the mention belongs to the authenticated user
        if ($mention->keyword->user_id !== Auth::id()) {
            abort(403);
        }

        return view('mentions.show', compact('mention'));
    }
} 
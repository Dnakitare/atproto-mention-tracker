<?php

namespace App\Http\Controllers;

use App\Models\TrackedKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeywordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keywords = Auth::user()->trackedKeywords()->get();
        return view('keywords.index', compact('keywords'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('keywords.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'type' => 'required|in:username,hashtag,keyword',
            'is_active' => 'boolean',
        ]);

        Auth::user()->trackedKeywords()->create([
            'keyword' => $request->keyword,
            'type' => $request->type,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('keywords.index')
            ->with('success', 'Keyword added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $keyword = Auth::user()->trackedKeywords()->findOrFail($id);
        return view('keywords.edit', compact('keyword'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'type' => 'required|in:username,hashtag,keyword',
            'is_active' => 'boolean',
        ]);

        $keyword = Auth::user()->trackedKeywords()->findOrFail($id);
        $keyword->update([
            'keyword' => $request->keyword,
            'type' => $request->type,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('keywords.index')
            ->with('success', 'Keyword updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keyword = Auth::user()->trackedKeywords()->findOrFail($id);
        $keyword->delete();

        return redirect()->route('keywords.index')
            ->with('success', 'Keyword deleted successfully.');
    }
} 
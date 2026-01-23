<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $showArchived = $request->boolean('show_archived', false);
        
        $query = Tag::where('user_id', $userId)
            ->withCount([
                'tasks' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ]);
        
        if (!$showArchived) {
            $query->where('is_archived', false);
        }
        
        $tags = $query->get();

        return Inertia::render('Tags/Index', [
            'tags' => TagResource::collection($tags)->resolve(),
            'showArchived' => $showArchived,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tags,name,NULL,id,user_id,' . $request->user()->id,
            ],
        ]);

        Tag::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Tag created successfully.');
    }

    public function show(Tag $tag, Request $request): Response
    {
        // Authorization: ensure tag belongs to user
        if ($tag->user_id !== $request->user()->id) {
            abort(403);
        }

        $tag->load('tasks.project', 'tasks.timeSessions');

        return Inertia::render('Tags/Show', [
            'tag' => new TagResource($tag),
        ]);
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        // Authorization: ensure tag belongs to user
        if ($tag->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tags,name,' . $tag->id . ',id,user_id,' . $request->user()->id,
            ],
        ]);

        $tag->update($validated);

        return redirect()->back()->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag, Request $request): RedirectResponse
    {
        // Authorization: ensure tag belongs to user
        if ($tag->user_id !== $request->user()->id) {
            abort(403);
        }

        $tag->delete();

        return redirect()->back()->with('success', 'Tag deleted successfully.');
    }

    public function archive(Tag $tag, Request $request): RedirectResponse
    {
        // Authorization: ensure tag belongs to user
        if ($tag->user_id !== $request->user()->id) {
            abort(403);
        }

        $tag->update(['is_archived' => true]);

        return redirect()->back()->with('success', 'Tag archived successfully.');
    }

    public function restore(Tag $tag, Request $request): RedirectResponse
    {
        // Authorization: ensure tag belongs to user
        if ($tag->user_id !== $request->user()->id) {
            abort(403);
        }

        $tag->update(['is_archived' => false]);

        return redirect()->back()->with('success', 'Tag restored successfully.');
    }
}

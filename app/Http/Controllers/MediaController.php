<?php

namespace App\Http\Controllers;

use App\Media;
use App\Style;
use App\Tag;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Http\Requests\PreviewMediaRequest;
use App\Http\Requests\StoreMediaRequest;
use Auth;
use Storage;
use App\Like;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media = Media::with(['user'])->withCount('likes')->withCount('comments')->withCount(['likes as liked' => function ($query) {
            $query->where('user_id', Auth::check() ? Auth::id() : 0);
        }]);

        $media = $media->get();

        if (request()->wantsJson()) {
            return response()->json($media);
        } else {
            $userId = Auth::id();
            return view('media.index', compact('media', 'userId'));
        }
    }

    public function photosByUserId($userId)
    {
        $media = Auth::user()->media()->get();

        if (request()->wantsJson()) {
            return response()->json($media);
        } else {
            $userId = Auth::id();
            return view('media.photosByUserId', compact('media', 'userId'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $styles = Style::all();
        $tags = Tag::all();

        return view('media.create', compact('styles', 'tags'));
    }

    public function preview(PreviewMediaRequest $request)
    {
        $imagePath = 'storage/' . $request->file('userPhoto')->store('media_upload', 'public');
        //return response()->json(asset($imagePath)); //uncomment this when you want to test but styling doesn't work
        $style = Style::find($request->style_id);
        $stylizedImagePath = "storage/media_stylized/" . str_replace(['/', '.', ','], '', str_random()) . '.jpg';
        
        // $process = new Process('python3 ' . base_path() . '/style_transfer.py --model ' . base_path($style->model_path) . " --image " . public_path("$imagePath") . " --output " . public_path("$stylizedImagePath"));
        // $process->run();

        // if (!$process->isSuccessful()) {
        //     throw new ProcessFailedException($process);
        //     return view('errors.media');
        // }

        return response()->json([
            'stylized_path' => $stylizedImagePath,
            'original_path' => $imagePath,
            'style_id' => $style->id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMediaRequest $request)
    {
        if (!file_exists(public_path($request->original_path)) || !file_exists(public_path($request->stylized_path))) {
            abort(422);
        }

        // $renamedOriginalPath = str_replace('temp_', '', $request->original_path);
        // $renamedStylizedPath = str_replace('temp_', '', $request->stylized_path);
        // Storage::disk('public')->move($request->original_path, $renamedOriginalPath);
        // Storage::disk('public')->move($request->stylized_path, $renamedStylizedPath);

        $media = Media::create([
            'user_id' => Auth::id(),
            'style_id' => $request->style_id,
            'path' => $request->original_path,
            'stylized_path' => $request->stylized_path,
        ]);

        $tagsList = [];
        foreach ($request->tags as $tag) {
            $tagsList[] = Tag::firstOrCreate(['name' => $tag])->id;
        }
        $media->tags()->sync($tagsList);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Picture saved successfully!',
                'model' => $media,
            ]);
        } else {
            return redirect()->route('media.show', $media->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function show(Media $media)
    {
        $media->load(['comments' => function ($query) {
            $query->withCount('likes');
            $query->withCount(['likes as liked' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);
        }, 'comments.replies' => function ($query) {
            $query->withCount('likes');
            $query->withCount(['likes as liked' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);
        }, 'tags']);
        $media->likes_count = $media->likes()->count();
        $media->liked = $media->likes()->where('user_id', Auth::check() ? Auth::id() : 0)->count();

        $userId = Auth::id();

        if (request()->wantsJson()) {
            return response()->json(['media' => $media]);
        } else {
            return view('media.show', compact('media', 'userId'));        
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit(Media $media)
    {
        return view('media.edit', compact('media'));        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function destroy(Media $media)
    {
        //
    }

    public function toggleLike(Media $media)
    {
        $userLike = $media->likes()->where('user_id', Auth::id())->first();

        if ($userLike) {
            // Unlike
            $userLike->delete();
        } else {
            // Like
            $like = new Like(['created_at' => now(), 'updated_at' => now(), 'user_id' => Auth::id()]);
            $media->likes()->save($like);
        }

        return response()->json([
            'status' => 'success',
            'message' => '',
        ]);
    }
}

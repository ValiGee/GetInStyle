<?php

namespace App\Http\Controllers;

use App\Media;
use App\Style;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Http\Requests\PreviewMediaRequest;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\SearchMediaRequest;
use Auth;
use Storage;
use App\Like;
use DB;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'sortColumn' => 'sometimes|string|in:likes_count,created_at',
            'sortOrder' => 'sometimes|string|in:asc,desc',
        ]);

        $sortColumn = [
            'Number of likes' => 'likes_count',
            'Date' => 'created_at',
        ];

        $sortOrder = [
            'Ascending' => 'asc',
            'Descending' => 'desc',
        ];

        $sortByColumn = $request->sortColumn ?? 'created_at';
        $sortByOrder = $request->sortOrder ?? 'desc';

        $media = Media::with(['user'])->withCount('likes')->withCount('comments')->withCount(['likes as liked' => function ($query) {
            $query->where('user_id', Auth::check() ? Auth::id() : 0);
        }])->orderBy($sortByColumn, $sortByOrder);

        $media = $media->paginate(50);

        if (request()->wantsJson()) {
            return response()->json($media);
        } else {
            $userId = Auth::id();
            return view('media.index', compact('media', 'userId', 'sortColumn', 'sortOrder', 'sortByColumn', 'sortByOrder'));
        }
    }

    public function photosByUserId($userId)
    {
        $user = User::findOrFail($userId);

        $media = $user->media()->get();

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
        
        $process = new Process('python3 ' . base_path() . '/style_transfer.py --model ' . base_path($style->model_path) . " --image " . public_path("$imagePath") . " --output " . public_path("$stylizedImagePath"));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
            return view('errors.media');
        }

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
            'description' => $request->description,
        ]);

        if ($request->tags) {
            $tagsList = [];
            foreach ($request->tags as $tag) {
                $tagsList[] = Tag::firstOrCreate(['name' => $tag])->id;
            }
            $media->tags()->sync($tagsList);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Picture saved successfully!',
            'model' => $media,
        ]);
    }

    public function search(SearchMediaRequest $request)
    {
        $sortColumn = [
            'Number of likes' => 'likes_count',
            'Date' => 'created_at',
        ];

        $sortOrder = [
            'Ascending' => 'asc',
            'Descending' => 'desc',
        ];

        $sortByColumn = $request->sortColumn ?? 'created_at';
        $sortByOrder = $request->sortOrder ?? 'desc';

        $tags = $request->tags;
        //$request->tags is an array containing a single string, all the tags
        $tagsStr = str_replace("#", "", $request->tags[0]);
        $tagsArr = explode(" ", $tagsStr);
        $tagNames = [];
        foreach ($tagsArr as $tag) {
            if ($tag != "") {
                array_push($tagNames, $tag);
            }
        }
        $tagNames = array_map('strtolower', $tagNames);

        //TODO : find media objects filtering by tagNames. Consider case insensitive names comparison
        //TODO : also need likes count, comments count, if i liked a media, etc. just like we do in 'index' page
        $media = Media::whereHas('tags', function ($query) use ($tagNames) {
            $query->whereIn(DB::raw('lower(name)'), $tagNames);
        })->withCount(['likes', 'comments', 'likes as liked' => function ($query) {
            $query->where('user_id', Auth::id());
        }])->orderBy($sortByColumn, $sortByOrder)->paginate(50);

        if (request()->wantsJson()) {
            return response()->json($media);
        } else {
            $userId = Auth::id();
            $searchPlaceholder = $request->tags[0];
            return view('media.search', compact('media', 'userId', 'searchPlaceholder', 'tags', 'sortColumn', 'sortOrder', 'sortByColumn', 'sortByOrder'));
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

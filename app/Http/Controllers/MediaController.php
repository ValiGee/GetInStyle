<?php

namespace App\Http\Controllers;

use App\Media;
use App\Style;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Http\Requests\StoreMediaRequest;
use Auth;
use Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media = Media::with(['user'])->withCount('likes')->get();

        return view('media.index', compact('media'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $styles = Style::all();
        
        return view('media.create', compact('styles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMediaRequest $request)
    {
        $imagePath = 'storage/' . $request->file('userPhoto')->store('media_upload', 'public');
        $style = Style::find($request->style_id);
        $stylizedImagePath = "storage/media_stylized/" . str_random() . '.jpg';
        // dd('python3 ' . base_path() . '/style_transfer.py --model ' . base_path($style->model_path) . " --image " . public_path("$imagePath") . " --output " . public_path("$stylizedImagePath"));
        $process = new Process('python3 ' . base_path() . '/style_transfer.py --model ' . base_path($style->model_path) . " --image " . public_path("$imagePath") . " --output " . public_path("$stylizedImagePath"));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
            return view('errors.media');
        }

        if (Auth::check()) {
            $media = Media::create([
                'user_id' => Auth::id(),
                'style_id' => $style->id,
                'path' => $imagePath,
                'stylized_path' => $stylizedImagePath,
            ]);

            return redirect()->route('media.show', ['id' => $media->id]);
        } else {
            Storage::disk('public')->delete(substr($imagePath, strpos($imagePath, 'media_upload')));
            return response()->download(public_path($stylizedImagePath))->deleteFileAfterSend(true);
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
        return view('media.show', compress('media'));        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit(Media $media)
    {
        return view('media.edit', compress('media'));        
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
}

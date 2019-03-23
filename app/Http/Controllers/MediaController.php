<?php

namespace App\Http\Controllers;

use App\Media;
use App\Style;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Http\Requests\StoreMediaRequest;

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
        $imagePath = $request->userPhoto->store('media_upload', 'public');
        $imageRelativePath = substr($imagePath, strpos($imagePath, 'media_upload'));
        $style = Style::find($request->style_id);
        $stylizedImagePath = "stylized-images/" . str_random() . '.jpg';
        $process = new Process('python3 ' . base_path() . '/style_transfer.py --model ' . base_path($style->model_path) . " --image $imagePath --output " . storage_path($stylizedImagePath));
        $process->run();

        if (!$process->isSuccessful()) {
            return view('errors.media');
        }

        if (Auth::check()) {
            $media = Media::create([
                'user_id' => Auth::id(),
                'style_id' => $style->id,
                'path' => $imageRelativePath,
                'stylized_path' => $stylizedImagePath,
            ]);

            return redirect()->route('media.show', ['id' => $media->id]);
        } else {
            Storage::disk('public')->delete($imageRelativePath);
            
            return Storage::download(asset($stylizedImagePath))->deleteFileAfterSend(true);
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
        return view('media.show');        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit(Media $media)
    {
        return view('media.edit');        
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

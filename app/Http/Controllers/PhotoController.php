<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoRequest;
use App\Models\Photo;
use App\Models\User;
use App\Services\PhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{

    protected $photo;
    protected $photoService;

    public function __construct(Photo $photo, PhotoService $photoService)
    {
        $this->photo = $photo;
        $this->photoService = $photoService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->photoService->allData($this->photo->all());
        return response()->json(
            $data
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \ImagickException
     */
    public function store(PhotoRequest $request)
    {
        $data = $request->all();
        $data['filename'] = $this->photoService->uploadImage($request->photo);
        $data['owner_id'] = Auth::guard('api')->user()->id;
        $createdData = $this->photo->create($data);
        return response()->json([
            'id' => $createdData['id'],
            'url' => asset("photos/api/$createdData->filename")
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Photo $photo)
    {
        $data = $photo->toArray();
        return response()->json([
            'id' => $data['id'],
            'name' => $data['name'],
            'url' => asset('photos/api'.$data['filename']),
            'owner_id' => $data['owner_id']
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param PhotoRequest $request
     * @param Photo $photo
     * @return \Illuminate\Http\Response
     * @throws \ImagickException
     */
    public function update(PhotoRequest $request, Photo $photo)
    {
        $data = $request->all();
        $this->photoService->removeImage($photo->filename);
        $data['filename'] = $this->photoService->uploadImage($data['photo']);
        $photo->update($data);
        return response()->json([$photo]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Photo $photo
     * @return void
     * @throws \Exception
     */
    public function destroy(Photo $photo)
    {
        $photo->delete();
    }

    public function share(User $user, Request $request)
    {
        $shareUserImages = $user->sharedPhoto()->pluck('id')->all();
        $sharePhoto = $this->photoService->getSharePhoto($request->photos);
        $user->sharedPhoto()->attach($sharePhoto);
        return response()->json([
            'existing_photos' => $sharePhoto
        ], 201);
    }
}

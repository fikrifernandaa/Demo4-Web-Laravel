<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    /** GET /api/media?title=death  -> daftar media (+ filter judul, pagination). */
    public function index(Request $request): AnonymousResourceCollection
    {
        $media = Media::titleLike($request->query('title'))
            ->withCount('users')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return MediaResource::collection($media);
    }

    /** GET /api/media/{media} -> detail media beserta pengguna yang mengoleksinya. */
    public function show(Media $media): MediaResource
    {
        return new MediaResource($media->load('users'));
    }

    /** POST /api/media -> 201 Created. */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->response()
            ->setStatusCode(201);
    }

    /** PATCH /api/media/{media} */
    public function update(StoreMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return new MediaResource($media);
    }

    /** DELETE /api/media/{media} (baris pivot ikut terhapus via cascadeOnDelete). */
    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json(['message' => 'Media berhasil dihapus']);
    }
}

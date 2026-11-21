<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /** GET /api/users -> daftar pengguna + jumlah media di koleksinya. */
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::withCount('media')->orderBy('id')->paginate(10)
        );
    }

    /** GET /api/users/{user} -> detail pengguna beserta koleksi medianya. */
    public function show(User $user): UserResource
    {
        return new UserResource($user->load('media')->loadCount('media'));
    }

    /** GET /api/users/{user}/collection -> koleksi media milik pengguna. */
    public function collection(User $user): AnonymousResourceCollection
    {
        return MediaResource::collection(
            $user->media()->orderBy('media_user.created_at')->paginate(10)
        );
    }

    /**
     * POST /api/users/{user}/collection  body: { "media_id": 1 }
     * Custom action: mengubah relasi (attach) tanpa membuat data Media baru.
     */
    public function addToCollection(AddToCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = (int) $request->validated('media_id');

        // Cek duplikat -> 409 Conflict (request valid, tapi bentrok dengan data yang ada).
        if ($user->media()->wherePivot('media_id', $mediaId)->exists()) {
            return $this->conflict();
        }

        try {
            $user->media()->attach($mediaId);
        } catch (UniqueConstraintViolationException) {
            // Jaring pengaman race condition: dua request bersamaan lolos cek di atas,
            // unique index pada pivot menolak yang kedua.
            return $this->conflict();
        }

        return (new MediaResource(Media::findOrFail($mediaId)))
            ->additional(['message' => 'Media berhasil ditambahkan ke koleksi'])
            ->response()
            ->setStatusCode(201);
    }

    /** DELETE /api/users/{user}/collection/{media} -> detach dari koleksi. */
    public function removeFromCollection(User $user, Media $media): JsonResponse
    {
        if ($user->media()->detach($media->id) === 0) {
            return response()->json(
                ['message' => 'Media tidak ada di koleksi pengguna'],
                404
            );
        }

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi']);
    }

    private function conflict(): JsonResponse
    {
        return response()->json(['message' => 'Media sudah ada di koleksi'], 409);
    }
}

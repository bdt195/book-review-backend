<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $limit = $request->query('limit', 10);
        $genres = Genre::paginate($limit)->items();

        return response()->json(
            [
                'status' => true,
                'data' => $genres
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validateGenre = Validator::make(
                $request->all(),
                [
                    'name' => 'required'
                ]
            );

            if ($validateGenre->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Request data is invalid'
                    ],
                    400
                );
            }

            $url_key = $request->get(
                'url_key',
                $request->get('name') . '-' . time()
            );

            $url_key = strtolower(str_replace(' ', '-',$url_key));

            if (Genre::where('url_key', $url_key)->first()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Url key already exists'
                    ],
                    400
                );
            }

            Genre::create(
                [
                    'name' => $request->get('name'),
                    'url_key' => $url_key
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Genre Created Successfully'
            ]);
        } catch (\Throwable $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $author = Genre::findOrFail($id);
            return response()->json(
                [
                    'status' => true,
                    'data' => $author
                ]
            );
        } catch (ModelNotFoundException $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Not Found'
                ],
                404
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function showByUrlKey(string $urlKey): \Illuminate\Http\JsonResponse
    {
        try {
            $author = Genre::where('url_key', $urlKey);
            if ($author->count()) {
                if (!$author->first()->trashed()) {
                    return response()->json(
                        [
                            'status' => true,
                            'data' => $author->first()
                        ]
                    );
                }

                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Not Found'
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Not Found'
                ],
                404
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $author = Genre::findOrFail($id);

            $author->name = $request->get('name');
            $url_key = $request->get('url_key');
            if ($url_key) {
                $url_key = strtolower(str_replace(' ', '-',$url_key));
                $author->url_key = $url_key;
            }
            $author->save();

            return response()->json([
                'status' => true,
                'message' => 'Genre Update Successfully'
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Not Found'
                ],
                404
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $author = Genre::findOrFail($id);
            $author->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Deleted'
                ]
            );
        } catch (ModelNotFoundException $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Not Found'
                ],
                404
            );
        } catch (\Throwable $exception) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }
}

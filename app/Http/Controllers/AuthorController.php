<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Author;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $limit = $request->query('limit', 10);
        $authors = Author::with('genres:genre_id,name');
        $authors->paginate($limit);

        return response()->json(
            [
                'status' => true,
                'data' => $authors->get()
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validateAuthor = Validator::make(
                $request->all(),
                [
                    'name' => 'required'
                ]
            );

            if ($validateAuthor->fails()) {
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

            if (Author::where('url_key', $url_key)->first()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Url key already exists'
                    ],
                    400
                );
            }

            $author = Author::create(
                [
                    'name' => $request->get('name'),
                    'url_key' => $url_key
                ]
            );

            $genreIds = $request->get('genres');
            if ($genreIds) {
                foreach ($genreIds as $genreId) {
                    $author->genres()->attach(intval($genreId));
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Author Created Successfully'
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
            $author = Author::with('genres:genre_id,name')->findOrFail($id);
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
            $author = Author::where('url_key', $urlKey);
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
            $author = Author::findOrFail($id);

            if ($request->get('name')) {
                $author->name = $request->get('name');
            }
            $url_key = $request->get('url_key');
            if ($url_key) {
                $url_key = strtolower(str_replace(' ', '-',$url_key));
                $author->url_key = $url_key;
            }
            $author->save();
            $genreIds = $request->get('genres');
            if ($genreIds) {
                $author->genres()->sync($genreIds);
            }

            return response()->json([
                'status' => true,
                'message' => 'Author Update Successfully'
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
            $author = Author::findOrFail($id);
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

<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Publisher;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $limit = $request->query('limit', 10);
        $publishers = Publisher::paginate($limit)->items();

        return response()->json(
            [
                'status' => true,
                'data' => $publishers
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatePublisher = Validator::make(
                $request->all(),
                [
                    'name' => 'required'
                ]
            );

            if ($validatePublisher->fails()) {
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

            if (Publisher::where('url_key', $url_key)->first()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Url key already exists'
                    ],
                    400
                );
            }

            Publisher::create(
                [
                    'name' => $request->get('name'),
                    'url_key' => $url_key
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Publisher Created Successfully'
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
            $publisher = Publisher::findOrFail($id);
            return response()->json(
                [
                    'status' => true,
                    'data' => $publisher
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
            $publisher = Publisher::where('url_key', $urlKey);
            if ($publisher->count()) {
                if (!$publisher->first()->trashed()) {
                    return response()->json(
                        [
                            'status' => true,
                            'data' => $publisher->first()
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
            $publisher = Publisher::findOrFail($id);

            if ($request->get('name')) {
                $publisher->name = $request->get('name');
            }
            $url_key = $request->get('url_key');
            if ($url_key) {
                $url_key = strtolower(str_replace(' ', '-',$url_key));
                $publisher->url_key = $url_key;
            }
            $publisher->save();

            return response()->json([
                'status' => true,
                'message' => 'Publisher Update Successfully'
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
            $publisher = Publisher::findOrFail($id);
            $publisher->delete();

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

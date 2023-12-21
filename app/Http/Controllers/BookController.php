<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            [
                'status' => true,
                'data' => Book::with('genres:genre_id,name')->get()
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validateBook = Validator::make(
                $request->all(),
                [
                    'title' => 'required'
                ]
            );

            if ($validateBook->fails()) {
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
                $request->get('title') . '-' . time()
            );

            $url_key = strtolower(str_replace(' ', '-',$url_key));

            if (Book::where('url_key', $url_key)->first()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Url key already exists'
                    ],
                    400
                );
            }

            $bookData = [
                'title' => $request->get('title')
            ];


            if ($request->get('title_complete')) {
                $bookData['title_complete'] = $request->get('title_complete');
            }
            if ($request->get('isbn')) {
                $bookData['isbn'] = $request->get('isbn');
            }
            if ($request->get('publisher_id')) {
                $bookData['publisher_id'] = $request->get('publisher_id');
            }
            if ($request->get('publish_date')) {
                $bookData['publish_date'] = $request->get('publish_date');
            }
            if ($request->get('image_url')) {
                $bookData['image_url'] = $request->get('image_url');
            }
            if ($request->get('description')) {
                $bookData['description'] = $request->get('description');
            }
            if ($request->get('num_pages')) {
                $bookData['num_pages'] = $request->get('num_pages');
            }
            if ($request->get('language')) {
                $bookData['language'] = $request->get('language');
            }
            if ($request->get('url_key')) {
                $bookData['url_key'] = $request->get('url_key');
            }

            $book = Book::create($bookData);

            $genreIds = $request->get('genres');
            if ($genreIds) {
                foreach ($genreIds as $genreId) {
                    $book->genres()->attach(intval($genreId));
                }
            }

            $authorIds = $request->get('authors');
            if ($authorIds) {
                foreach ($authorIds as $authorId) {
                    $book->authors()->attach(intval($authorId));
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Book Created Successfully'
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
            $book = Book::with('genres:genre_id,name')
                ->with('authors:author_id,name')
                ->with('publisher:publisher_id,name')
                ->findOrFail($id);

            return response()->json(
                [
                    'status' => true,
                    'data' => $book
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
            $book = Book::where('url_key', $urlKey);
            if ($book->count()) {
                if (!$book->first()->trashed()) {
                    return response()->json(
                        [
                            'status' => true,
                            'data' => $book->first()
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
            $book = Book::findOrFail($id);

            if ($request->get('title')) {
                $book->name = $request->get('title');
            }

            if ($request->get('title_complete')) {
                $book->title_complete = $request->get('title_complete');
            }

            if ($request->get('isbn')) {
                $book->isbn = $request->get('isbn');
            }

            $url_key = $request->get('url_key');
            if ($url_key) {
                $url_key = strtolower(str_replace(' ', '-',$url_key));
                $book->url_key = $url_key;
            }

            if ($request->get('publisher_id')) {
                $book->publisher_id = $request->get('publisher_id');
            }
            if ($request->get('publish_date')) {
                $book->publish_date = $request->get('publish_date');
            }
            if ($request->get('image_url')) {
                $book->image_url = $request->get('image_url');
            }
            if ($request->get('description')) {
                $book->description = $request->get('description');
            }
            if ($request->get('num_pages')) {
                $book->num_pages = $request->get('num_pages');
            }
            if ($request->get('language')) {
                $book->language = $request->get('language');
            }

            $book->save();
            $genreIds = $request->get('genres');
            if ($genreIds) {
                $book->genres()->sync($genreIds);
            }

            $authorIds = $request->get('authors');
            if ($authorIds) {
                $book->genres()->sync($authorIds);
            }

            return response()->json([
                'status' => true,
                'message' => 'Book Update Successfully'
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
            $book = Book::findOrFail($id);
            $book->delete();

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

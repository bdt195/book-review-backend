<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Bad Request',
                        'errors' => $validateUser->errors()
                    ],
                    400
                );
            }

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password'))
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
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

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Bad Request',
                        'errors' => $validateUser->errors()
                    ],
                    400
                );
            }

            if (!Auth::attempt($request->only(['name', 'password']))) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Username and password does not match.'
                    ],
                    401
                );
            }

            $user = User::where('name', $request->get('name'))->first();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Login Successfully',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ]
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

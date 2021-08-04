<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login', 'register']]);
    }

    public function register(RegisterRequest $request){
        $data = [
            'name'     => $request -> input('name'),
            'email'    => $request -> input('email'),
            'password' => bcrypt( $request -> input('password') )
        ];
        User::create($data);

        return $this -> login(LoginRequest::createFrom($request));
    }

    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Email or Password'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function user(): UserResource
    {
        return (new UserResource(auth() -> user()));
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): UserResource
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token): UserResource
    {
        return (new UserResource(auth() ->user()))->additional([
            'meta' => [
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => auth() -> factory() -> getTTL() * 60,
            ]
        ]);
    }
}

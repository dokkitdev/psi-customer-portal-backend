<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\CheckRestoreTokenRequest;
use App\Http\Requests\Auth\ConfirmEmailRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RestorePasswordRequest;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request, UserService $service, JWTAuth $auth)
    {
        $credentials = $request->only('email', 'password');
        $token = false;

        $user = $service->getByEmailInsensitively($credentials['email']);

        if ($user && Hash::check($credentials['password'], $user['password'])) {
            $token = $auth->fromUser($user);
        }

        if ($token === false) {
            return response()->json([
                'message' => 'Authorization failed'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'token' => $token,
            'ttl' => config('jwt.ttl'),
            'refresh_ttl' => config('jwt.refresh_ttl'),
            'user' => $user
        ]);
    }

    public function refreshToken(RefreshTokenRequest $request)
    {
        return response('', Response::HTTP_NO_CONTENT);
    }

    public function forgotPassword(ForgotPasswordRequest $request, UserService $service)
    {
        $service->forgotPassword($request->input('email'));

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function restorePassword(RestorePasswordRequest $request, UserService $service)
    {
        $service->restorePassword(
            $request->input('token'),
            $request->input('password')
        );

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function confirmEmail(ConfirmEmailRequest $request, UserService $service)
    {
        $service->confirmEmail($request->input('token'));

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function checkRestoreToken(CheckRestoreTokenRequest $request)
    {
        return response('', Response::HTTP_NO_CONTENT);
    }
}

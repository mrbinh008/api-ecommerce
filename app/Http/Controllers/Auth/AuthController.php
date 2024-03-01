<?php

namespace App\Http\Controllers\Auth;

use App\Events\TestMail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\MTest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * This method handles the registration of a new user.
     *
     * @param Request $request The incoming HTTP request
     * @return \Illuminate\Http\JsonResponse Returns a JSON response with the access token and user data
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('user');

        event(new Registered($user));

        $token = $user->createToken('token-api')->accessToken;

        return responseCustom(
            [
                'access_token' => $token,
                'user' => $user
            ],
            201,
            'Register Success'
        );
    }

    /**
     * Handle an incoming authentication request.
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $request->ensureIsNotRateLimited();
        $user = User::where('email', $request->email)->first();
        if (!$user || !\Hash::check($request->password, $user->password)) {
            RateLimiter::hit($request->throttleKey());
            return responseCustom(
                [],
                401,
                'Unauthorized'
            );
        }
        RateLimiter::clear($request->throttleKey());
        $token = $user->createToken('token-api')->accessToken;
        $userDetail=[
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number'=>$user->phone_number,
            'avatar'=>Env::get('APP_URL').'/'.$user->avatar,
            'is_active'=>$user->is_active,
            'role'=>$user->getRoleNames()->first(),
        ];

        return responseCustom(
            [
                'access_token' => $token,
                'user' => $userDetail,
            ],
            200,
            'Login Success'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return responseCustom(
            [],
            200,
            'Logout Success'
        );
    }

    public function refreshToken()
    {
        $token = auth()->user()->createToken('token-api')->accessToken;
        return responseCustom(
            [
                'access_token' => $token,
                'user' => auth()->user()
            ],
            200,
            'Refresh Token Success'
        );
    }

    public function test()
    {
        event(new TestMail('test@gmail.com', 'Test'));
        dd('test');
    }
}

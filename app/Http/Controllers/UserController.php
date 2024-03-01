<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
//        $user = \Cache::remember('users'.auth()->user()->id, 60 * 60 * 24, function () {
//            return User::find(auth()->user()->id);
//        });
        $user = User::find(auth()->user()->id);
        $user->avatar = Env::get('APP_URL') . '/' . $user->avatar;
        if (!$user) return responseCustom(null, Response::HTTP_OK, 'User not found');
        return responseCustom($user, Response::HTTP_OK, 'Get user success');
    }


    public function update(UserRequest $request)
    {
        $user = User::find(auth()->guard('api')->user()->id);
        if ($user) {
            try {
                $avatar = $user->avatar;
                if ($request->hasFile('avatar')) {
                    $avatar = $this->updateImage($request, 'avatar', 'uploads/avatars', $user->avatar)['path'];
                }
                $user->name = $request->name;
                $user->email = $request->email;
                $user->avatar = $avatar;
                $user->is_active = $request->is_active;
                $user->syncRoles([$request->role]);
                $user->save();
                $user->avatar = Env::get('APP_URL') . '/' . $user->avatar;
                return responseCustom($user, Response::HTTP_OK, 'Update user success');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return responseCustom([], 500, 'Server errors',);
            }
        } else {
            return responseCustom([], Response::HTTP_OK, 'User not found');
        }
    }

    public function destroy()
    {
        $user = User::find(auth()->user()->id);
        if ($user) {
            try {
                $user->delete();
                $this->deleteImage($user->avatar);
                return responseCustom([], Response::HTTP_OK, 'Delete user success');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return responseCustom([], 500, 'Delete user fail', $e->getMessage());
            }
        } else {
            return responseCustom([], Response::HTTP_OK, 'User not found');
        }
    }

    public function changePassword(UserRequest $request)
    {
        $user = User::find(auth()->user()->id);
        if ($user) {
            try {
                if (!\Hash::check($request->old_password, $user->password)) {
                    return responseCustom([], 400, 'Old password is incorrect');
                }
                $user->password = bcrypt($request->password);
                $user->save();
                return responseCustom([], Response::HTTP_OK, 'Change password success');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return responseCustom([], 500, 'Change password fail');
            }
        } else {
            return responseCustom([], Response::HTTP_OK, 'User not found');
        }
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    use ImageUploadTrait;

    public function index(Request $request)
    {
        if ($request->has('id')) {
            $user = User::find($request->id);
            if (!$user) return responseCustom(null, 200, 'User not found');
            return responseCustom($user, 200, 'Get user success');
        }
        $user = User::getListCustomer($request->limit ?? 10);

        $data = $user->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->getRoleNames(),
                'status' => $user->is_active,
                'created_at' => $user->created_at,
            ];
        });
        return responsePaginate($user, $data, 200, 'Get list user success');
    }

    public function store(UserRequest $request)
    {
        $avatar = null;
        try {
            if ($request->hasFile('avatar')) {
                $avatar = $this->uploadImage($request, 'avatar', 'uploads/avatars');
            }
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->avatar = $avatar;
            $user->is_active = $request->is_active;
            $user->assignRole($request->role);
            $user->save();
            return responseCustom($user, 200, 'Create user success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return responseCustom(null, 500, 'Create user fail', $e->getMessage());
        }
    }

    public function show(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            return responseCustom($user, 200, 'Get user success');
        } else {
            return responseCustom(null, 404, 'User not found');
        }
    }

    public function update(UserRequest $request)
    {
        $user = User::find($request->id);
        if ($user) {
            try {
                $avatar = $user->avatar;
                if ($request->hasFile('avatar')) {
                    $avatar = $this->updateImage($request, 'avatar', 'uploads/avatars', $user->avatar);
                }
                $user->name = $request->name;
                $user->email = $request->email;
                $user->avatar = $avatar;
                $user->is_active = $request->is_active;
                $user->syncRoles([$request->role]);
                $user->save();
                return responseCustom($user, 200, 'Update user success');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return responseCustom([], 500, 'Server errors',);
            }
        } else {
            return responseCustom([], 200, 'User not found');
        }
    }

    public function destroy(UserRequest $request)
    {
        $user = User::find($request->id);
        if ($user) {
            try {
                $user->delete();
                $this->deleteImage($user->avatar);
                return responseCustom([], 200, 'Delete user success');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return responseCustom([], 500, 'Delete user fail', $e->getMessage());
            }
        } else {
            return responseCustom([], 404, 'User not found');
        }
    }

    public function changeStatus(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            $user->is_active = !$user->is_active;
            $user->save();
            return responseCustom($user, 200, 'Change status success');
        } else {
            return responseCustom([], 404, 'User not found');
        }
    }

    public function search(Request $request)
    {
        if ($request->has('q')) {
            $user = User::searchCustomer($request->q, $request->limit ?? 10);
            $data = $user->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'role' => $user->getRoleNames(),
                    'status' => $user->is_active,
                    'created_at' => $user->created_at,
                ];
            });
            return responsePaginate($user, $data, 200, 'Search user success');
        } else {
            return responseCustom(null, 400, 'Search field is required');
        }
    }

//    public function test(Request $request)
//    {
//        $avatar = $this->uploadMultiImage($request, 'avatar', 'uploads/avatars');
//        return responseCustom($avatar, 200, 'Upload image success');
//    }

}

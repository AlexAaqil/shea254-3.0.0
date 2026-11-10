<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Modules\User\Http\Requests\UserRequest;
use Modules\User\Models\User;

class UserController extends Controller
{
    public function create()
    {
        return inertia('app/users/Create');
    }

    public function store(UserRequest $request)
    {
        $validated_data = $request->validated();

        $data['password'] = Hash::make($validated_data['password']);

        User::create($validated_data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'User created successfully'
        ]);

        return to_route('users.index');
    }

    public function edit(User $user)
    {
        return view('pages.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated_data = $request->validated();

        if($request->filled('password')) {
            $validated_data['password'] = Hash::make($validated_data['password']);
        } else {
            unset($validated_data['password']);
        }

        $user->update($validated_data);

        session()->flash('notify', ['message' => 'user updated successfully', 'type' => 'success']);

        return redirect()->route('users.index');
    }
}

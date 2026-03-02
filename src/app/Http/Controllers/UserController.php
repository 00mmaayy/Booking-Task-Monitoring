<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Show the form for registering a new user.
     */
    public function create(Request $request): View
    {
        $this->ensureCanManageUsers($request);

        return view('users.register', [
            'roles' => User::roles(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureCanManageUsers($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:'.implode(',', User::roles())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => User::STATUS_ACTIVE,
        ]);

        event(new Registered($user));

        return Redirect::route('settings.index')->with('status', 'user-created');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Request $request, User $user): View
    {
        $this->ensureCanManageUsers($request);

        return view('users.edit', [
            'user' => $user,
            'roles' => User::roles(),
            'statuses' => User::statuses(),
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->ensureCanManageUsers($request);

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('settings.index')->with('status', 'user-updated');
    }

    /**
     * @throws AuthorizationException
     */
    protected function ensureCanManageUsers(Request $request): void
    {
        $request->user()?->can('manage-users') ?: throw new AuthorizationException();
    }
}

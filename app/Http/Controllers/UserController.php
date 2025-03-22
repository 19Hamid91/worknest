<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return Inertia::render('User/Index');
    }

    public function all(Request $request)
    {
        $query = User::query();

        // Searching
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('username', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->has('sortField') && $request->has('sortOrder')) {
            $sortField = $request->sortField;
            $sortOrder = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('id');
        }

        // Pagination
        $users = $query->paginate($request->get('rows', 10));

        return response()->json([
            'data' => $users->items(),
            'total' => $users->total()
        ]);
    }

    public function create()
    {
        return Inertia::render('User/Create', [
            'defaultImage' => asset('assets/img/default-user.svg')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|regex:/^\+?[0-9]+$/|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('users', 'public');
            }

            $user = $this->userService->createUser($validated);

            return redirect()->route('user.index')->with('success', 'User created successfully');
        } catch (\Throwable $th) {
            return back()->withErrors('error', 'Failed to create user: ' . $th->getMessage());
        }
    }

    public function edit(User $user)
    {
        return Inertia::render('User/Edit', [
            'user' => $user,
            'defaultImage' => asset('assets/img/default-user.svg')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|regex:/^\+?[0-9]+$/|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $validated['photo'] = $user->photo;
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('users', 'public');

                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
            }
            $user = $this->userService->updateUser($user, $validated);

            return redirect()->route('user.index')->with('success', 'User updated successfully');
        } catch (\Throwable $th) {
            return back()->withErrors('error', 'Failed to update user: ' . $th->getMessage());
        }
    }
}

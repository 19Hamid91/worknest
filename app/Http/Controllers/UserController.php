<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
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
}

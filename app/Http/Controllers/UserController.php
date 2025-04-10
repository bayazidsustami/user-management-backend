<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'string|max:20',
            'address' => 'string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = Auth::user();
        $data = $request->only(['name', 'email', 'phone', 'address']);

        if ($request->hasFile('photo')) {
            try {
                if ($user->photo) {
                    $oldPath = str_replace(url('storage/'), '', $user->photo);
                    if (file_exists(storage_path('app/public/' . $oldPath))) {
                        unlink(storage_path('app/public/' . $oldPath));
                    }
                }

                $photoPath = $request->file('photo')->store('users', 'public');
                
                if (!$photoPath) {
                    throw new \Exception('Failed to store photo');
                }
                $data['photo'] = $photoPath; 
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to upload photo: ' . $e->getMessage()
                ], 500);
            }
        }

        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function index(Request $request)
    {
        $query = User::query();
        
        // Exclude current user
        $query->where('id', '!=', Auth::id());

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $users = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        return Inertia::render('Profile', [
            'user' => Auth::user(),
        ]);
    }

    public function submit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'current_password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => "Input invalid, try again!"
                ], 200);
            }

            $user = User::findOrFail(Auth::user()->id);

            // Check if old password is valid 
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    "message" => "Invalid password!"
                ], );
            }
            
            
            // Check email
            $check_email = User::where('email', $request->email)->count();
            if ($check_email > 1) {
                return response()->json([
                    "message" => "The email has already been taken."
                ], 200);
            }
            
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->new_password) {
                // Create new password
                $new_password = Hash::make($request->new_password, [
                    "rounds" => 12
                ]);
                $user->password = $new_password;
            }
            
            $user->save();
            
            return response()->json([
                "data" => "OK",
                "message" => "Profile has been changed!"
            ], 200);

        } catch (\Error $err) {
            
            return response()->json([
                'error' => $err->getMessage(),
                'message' => "Something went wrong on server, try again!"
            ], 200);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{

    public function index()
    {
        $mentors = Mentor::all();

        return response()->json([
            'status' => 'success',
            'data' => $mentors,
        ]);
    }

    // get mentor by id
    public function show($id)
    {
        $mentor = Mentor::find($id);

        if(!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mentor,
        ]);
    }

    public function create(Request $request)
    {
        // Validate the request...
        $rules = [
            'name' => 'required|string',
            'profile' => 'required|url',
            'profession' => 'required|string',
            'email' => 'required|email',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        // If validation passes, create mentor
        $mentor = Mentor::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $mentor,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the request...
        $rules = [
            'name' => 'string',
            'profile' => 'url',
            'profession' => 'string',
            'email' => 'email',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        // If validation passes, update mentor
        $mentor = Mentor::find($id);

        if(!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor not found',
            ], 404);
        }

        // Fill the mentor with the new data
        $mentor->fill($data);
        
        $mentor->save();

        return response()->json([
            'status' => 'success',
            'data' => $mentor,
        ]);
    }
}

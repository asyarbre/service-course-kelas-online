<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'course_id' => 'required|integer',
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

        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        // if course not found, return error response
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found',
            ], 404);
        }

        // create new chapter
        $chapter = Chapter::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $chapter,
        ]);
    }
}

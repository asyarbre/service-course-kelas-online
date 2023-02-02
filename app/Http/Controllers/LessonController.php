<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'video' => 'required|string',
            'chapter_id' => 'required|integer',
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

        // check if chapter exists
        $chapterId = $request->input('chapter_id');
        $chapter = Chapter::find($chapterId);

        // If chapter not found, return error response
        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found',
            ], 404);
        }

        // create lesson
        $lesson = Lesson::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $lesson,
        ]);
    }
}

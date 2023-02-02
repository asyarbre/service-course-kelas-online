<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyCourse;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'course_id' => 'required|integer',
            'user_id' => 'required|integer',
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
        
        // check if user is already
        $userId = $request->input('user_id');
        $user = getUser($userId);

        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message'],
            ], $user['http_code']);
        }

        // prevent user from buying the same course
        $isExistMyCourse = MyCourse::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->exists();

        // if user already buy the course, return error response
        if ($isExistMyCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'user already buy this course',
            ], 409);
        }

        // create new my course
        $myCourse = MyCourse::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $myCourse,
        ]);
    }
}

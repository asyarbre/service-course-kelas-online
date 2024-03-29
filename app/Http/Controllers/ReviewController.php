<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'user_id' => 'required|integer',
            'course_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'note' => 'required|string',
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

        // check if course exists
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        // If course not found, return error response
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found',
            ], 404);
        }

        // check if user exists
        $userId = $request->input('user_id');
        $user = getUser($userId);

        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message'],
            ], $user['http_code']);
        }

        // check if user already reviewed the course
        $isExistReview = Review::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->exists();

        if ($isExistReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'user already reviewed the course',
            ], 409);
        }

        // create review
        $review = Review::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $review,
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'rating' => 'integer|min:1|max:5',
            'note' => 'string',
        ];

        $data = $request->except('user_id', 'course_id');

        $validator = Validator::make($data, $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        // find review by id
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'review not found',
            ], 404);
        }

        // update review
        $review->fill($data);
        $review->save();

        return response()->json([
            'status' => 'success',
            'data' => $review,
        ]);
    }

    public function destroy($id)
    {
        // find review by id
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'review not found',
            ], 404);
        }

        // delete review
        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'review deleted',
        ]);
    }
}

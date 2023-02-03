<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\MyCourse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query();

        // filter 
        $q = $request->input('q');
        $status = $request->query('status');

        // filter by name
        $courses->when($q, function ($query) use ($q) {
            return $query->whereRaw("name LIKE '%" . strtolower($q) . "%'");
        });

        // filter by status
        $courses->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        });

        return response()->json([
            'status' => 'success',
            'data' => $courses->paginate(10),
        ]);
    }

    public function show($id)
    {
        $course = Course::find($id);

        // If course not found, return error response
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found',
            ], 404);
        }

        // get review and total student
        $reviews = Review::where('course_id', "=", $id)->get()->toArray();
        $totalStudent = MyCourse::where('course_id', "=", $id)->count();

        $course['reviews'] = $reviews;
        $course['total_student'] = $totalStudent;

        return response()->json([
            'status' => 'success',
            'data' => $course,
        ]);
    }

    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'certificate' => 'required|boolean',
            'thumbnail' => 'url',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'required|integer',
            'level' => 'required|in:all-level,beginner,intermediate,advanced',
            'mentor_id' => 'required|integer',
            'description' => 'string',
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

        $mentorId = $request->input('mentor_id');
        $mentor = Mentor::find($mentorId);

        // If mentor not found, return error response
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor not found',
            ], 404);
        }

        // Create course
        $course = Course::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $course,
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => 'url',
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer',
            'level' => 'in:all-level,beginner,intermediate,advanced',
            'mentor_id' => 'integer',
            'description' => 'string',
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

        // Find course by id
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found',
            ], 404);
        }

        // check if mentor_id is provided
        $mentor_id = $request->input('mentor_id');
        if ($mentor_id) {
            $mentor = Mentor::find($mentor_id);

            // If mentor not found, return error response
            if (!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mentor not found',
                ], 404);
            }
        }

        // Update course
        $course->fill($data);
        $course->save();

        return response()->json([
            'status' => 'success',
            'data' => $course,
        ]);
    }

    public function destroy($id)
    {
        // Find course by id
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found',
            ], 404);
        }

        // Delete course
        $course->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Course deleted',
        ]);
    }
}

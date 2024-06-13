<?php

namespace App\Http\Controllers;

use App\Models\appointments;
use App\Models\Doctor;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get doctor's appointments, patients va display on dashboard
        $doctor = Auth::user();
        $appointments = appointments::where('doc_id', $doctor->id)->where('status', 'upcoming')->get();
        $review = Reviews::where('doc_id', $doctor->id)->where('status', 'active')->get();

        //tra ve du lieu tren dashboard 
        return view('dashboard')->with(['doctor'=>$doctor, 'appointments'=>$appointments, 'reviews'=>$review ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     //this controller  is to store booking details post from mobile app
    //     $reviews = new Reviews();
    //     //update the appointment status from "upcoming" to "complete"
    //     $appointment = appointments::where('id', $request->get('appointment_id'))->first();

    //     //save the ratings and reviews from user
    //     $reviews->user_id = Auth::guard('sanctum')->user()->id;
    //     $reviews->doc_id = $request->get('doctor_id');
    //     $reviews->ratings = $request->get('ratings');
    //     $reviews->reviews = $request->get('reviews');
    //     $reviews->reviewed_by = Auth::user()->name ;
    //     $reviews->status = 'active';
    //     $reviews->save();

    //     //change appointment status
    //     $appointment->status = 'complete';
    //     $appointment->save();

    //     return response()->json([
    //         'success'=>'The appointment has been completed and reviewed successfully!',
    //     ], 200);
    // }

    public function store(Request $request)
{
    // Retrieve the authenticated user
    $user = Auth::guard('sanctum')->user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    // Manually validate the incoming request data
    $appointmentId = $request->get('appointment_id');
    $doctorId = $request->get('doctor_id');
    $ratings = $request->get('ratings');
    $reviews = $request->get('reviews');

    $errors = [];

    if (!$appointmentId || !appointments::where('id', $appointmentId)->exists()) {
        $errors['appointment_id'] = 'The selected appointment id is invalid.';
    }

    if (!$doctorId || !Doctor::where('doc_id', $doctorId)->exists()) {
        $errors['doc_id'] = 'The selected doctor id is invalid.';
    }

    if (!$ratings || !is_numeric($ratings) || $ratings < 1 || $ratings > 5) {
        $errors['ratings'] = 'The ratings must be an integer between 1 and 5.';
    }

    if (!$reviews || !is_string($reviews) || strlen($reviews) > 255) {
        $errors['reviews'] = 'The reviews must be a string with a maximum length of 255 characters.';
    }

    if (!empty($errors)) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 422);
    }

    // Retrieve the appointment
    $appointment = appointments::find($appointmentId);
    if (!$appointment) {
        return response()->json(['error' => 'Appointment not found'], 404);
    }
    try {
        // Save the ratings and reviews from user
        $review = new Reviews();
        $review->user_id = $user->id;
        $review->doc_id = $doctorId;
        $review->ratings = $ratings;
        $review->reviews = $reviews;
        $review->reviewed_by = $user->name; // Use the authenticated user's name
        $review->status = 'active';
        $review->save();

        // Update the appointment status from "upcoming" to "complete"
        $appointment->update(['status' => 'complete']);
        
        return response()->json([
            'success' => 'The appointment has been completed and reviewed successfully!',
        ], 200);
    } catch (\Exception $e) {
        // Catch any exceptions and return a JSON response with the error
        return response()->json([
            'message' => 'An error occurred while saving the review or updating the appointment',
            'error' => $e->getMessage(),
        ], 500);
    }

}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

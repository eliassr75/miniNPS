<?php

namespace App\Http\Controllers;

use App\Models\NpsAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class NpsAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        //$cookie = Cookie::make('user_answer', 'dark_mode', 60 * 24 * 90, null, null, true, true);
        //$cookie = Cookie::make('user_entity_token', 'dark_mode', 60 * 24 * 90, null, null, true, true);
    }

    /**
     * Display the specified resource.
     */
    public function show(NpsAnswer $npsAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NpsAnswer $npsAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NpsAnswer $npsAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NpsAnswer $npsAnswer)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Paid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Paid  $paid
     * @return \Illuminate\Http\Response
     */
    public function show(Paid $paid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Paid  $paid
     * @return \Illuminate\Http\Response
     */
    public function edit(Paid $paid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Paid  $paid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Paid $paid)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Paid  $paid
     * @return \Illuminate\Http\Response
     */
    public function destroy(Paid $paid)
    {
        //
    }
}

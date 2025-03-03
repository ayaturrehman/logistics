<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoodsTypeRequest;
use App\Http\Requests\UpdateGoodsTypeRequest;
use App\Models\GoodsType;

class GoodsTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(GoodsType::all());
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
    public function store(StoreGoodsTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodsType $goodsType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodsType $goodsType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGoodsTypeRequest $request, GoodsType $goodsType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsType $goodsType)
    {
        //
    }
}

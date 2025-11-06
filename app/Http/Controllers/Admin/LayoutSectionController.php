<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayoutSection;
use Illuminate\Http\Request;

class LayoutSectionController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => LayoutSection::orderBy('position')->get()
        ]);
    }

    public function store(Request $request)
    {
        $section = LayoutSection::create($request->all());
        return response()->json(['success' => true, 'data' => $section]);
    }

    public function update(Request $request, LayoutSection $layoutSection)
    {
        $layoutSection->update($request->all());
        return response()->json(['success' => true, 'data' => $layoutSection]);
    }

    public function destroy(LayoutSection $layoutSection)
    {
        $layoutSection->delete();
        return response()->json(['success' => true]);
    }
}

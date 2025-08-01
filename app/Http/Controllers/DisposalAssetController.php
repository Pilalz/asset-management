<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisposalAsset;
use App\Models\Department;

class DisposalAssetController extends Controller
{
    public function index()
    {
        $disposalassets = DisposalAsset::all();
        $departments = Department::all();
        
        return view('disposal-asset.index', compact('departments', 'disposalassets'));
    }

    public function create()
    {
        $departments = Department::all();
        
        return view('disposal-asset.create', compact('departments'));
    }
}
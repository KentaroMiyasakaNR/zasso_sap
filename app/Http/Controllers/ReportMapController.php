<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportMapController extends Controller
{
    public function index()
    {
        $reports = Report::with('user')->orderBy('created_at', 'desc')->get();
        return view('report-map.index', compact('reports'));
    }
}

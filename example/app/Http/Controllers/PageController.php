<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function get(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)->first();
        if (!$page) {
            abort(404, 'Page not found');
        }
        return view('page', compact('page'));
    }
}

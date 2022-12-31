<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Customberg\PHP\Customberg;
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

    public function change_lang(Request $request, $lang)
    {
        $config = Customberg::getConfig();
        if (!isset($config['languages'][$lang])) {
            abort(404, 'Language not found');
        }
        session(['lang' => $lang]);
        return redirect()->back();
    }
}

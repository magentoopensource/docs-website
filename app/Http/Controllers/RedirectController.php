<?php

namespace App\Http\Controllers;

use App\Services\Redirects;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    /**
     * Fallback for every GET that matched no route: 301 when the path is in the redirect map, 404 as before otherwise.
     */
    public function fallback(Request $request)
    {
        $to = Redirects::resolve($request->getPathInfo());

        if ($to === null) {
            abort(404);
        }

        $query = $request->getQueryString();

        return redirect($to . ($query ? '?' . $query : ''), 301);
    }
}

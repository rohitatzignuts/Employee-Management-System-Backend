<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Actor;
use App\Models\Production;

require_once app_path('Http/Helpers/APIResponse.php');

class MovieController extends Controller
{
    public function index(Request $request)
    {
        try {
            $actors = Actor::with('movies')->get();
            return ok('Results Found!', $actors);
        } catch (\Throwable $th) {
            return error($th->getMessage());
        }
    }
}

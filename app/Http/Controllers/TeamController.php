<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View as FacadeView;
use Illuminate\Support\Facades\View;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return FacadeView
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function index(): FacadeView
    {
        return View::make(
            'teams.teams',
            [
                'name' => 'Teams'
            ]
        );
    }
}

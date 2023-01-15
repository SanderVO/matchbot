<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.å
     *
     * @return FacadeView
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function index()
    {
        return View::make(
            'organizations.organizations',
            [
                'name' => 'Organizations'
            ]
        );
    }
}
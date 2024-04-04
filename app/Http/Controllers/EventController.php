<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View as FacadeView;
use Illuminate\Support\Facades\View;

class EventController extends Controller
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
            'events.events',
            [
                'name' => 'Events'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Event $event
     * 
     * @return FacadeView
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function show(Event $event): FacadeView
    {
        return View::make(
            'events.event',
            [
                'event' => $event
            ]
        );
    }

    /**
     * Create a new resource.
     *
     * @param Event $event
     * 
     * @return FacadeView
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function create(): FacadeView
    {
        $event = new Event();

        return View::make(
            'events.create',
            [
                'event' => $event
            ]
        );
    }
}

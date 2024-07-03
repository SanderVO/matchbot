<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View as FacadeView;
use Illuminate\Support\Facades\View;
use Livewire\WithPagination;

class EventController extends Controller
{
    use WithPagination;

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
        $event
            ->loadMissing([
                'season',
                'eventType',
                'teamResults' => function ($query) {
                    $query
                        ->with([
                            'team.users',
                            'teamResultUsers.user'
                        ]);
                }
            ]);

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

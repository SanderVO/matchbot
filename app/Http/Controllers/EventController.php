<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View as FacadeView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     *
     * @return FacadeView
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function index(Request $request): FacadeView
    {
        $events = Event::query()
            ->with([
                'eventParticipants' => function ($query) {
                    $query
                        ->with([
                            'user',
                            'team.results'
                        ]);
                }
            ])
            ->orderBy(
                $request->query('orderBy', 'created_at'),
                $request->query('orderDirection', 'desc')
            )
            ->paginate((int) $request->query('limit'));

        return View::make('events.events', ['name' => 'events', 'events' => $events]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        return View::make('events.event', ['name' => 'events', 'event' => $event]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

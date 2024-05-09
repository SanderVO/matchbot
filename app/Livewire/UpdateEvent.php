<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\TeamResult;
use App\Models\TeamResultUser;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UpdateEvent extends Component
{
    public Event $event;

    public $comment = '';
    public $teamResults = [];

    public bool $saveIsSuccessful = false;

    /**
     * On mount
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function mount($eventId = null)
    {
        $this->event = Event::query()
            ->where('id', $eventId)
            ->with([
                'teamResults' => function ($query) {
                    $query
                        ->with([
                            'team',
                            'teamResultUsers' => function ($query) {
                                $query
                                    ->with([
                                        'user'
                                    ]);
                            }
                        ]);
                }
            ])
            ->first();

        $this->comment = $this->event->comment;

        $this->teamResults = $this->event->teamResults->toArray();
    }

    /**
     * Rules for form
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function rules()
    {
        return [
            'comment' => [
                'required',
                'string'
            ],
            'teamResults' => [
                'required',
                'array'
            ],
            'teamResults.*.id' => [
                'required',
                'numeric'
            ],
            'teamResults.*.score' => [
                'required',
                'numeric'
            ],
            'teamResults.*.crawl_score' => [
                'required',
                'numeric'
            ],
            'teamResults.*.team_result_users' => [
                'array'
            ],
            'teamResults.*.team_result_users.*.id' => [
                'required_with:teamResults.*.team_result_users',
                'numeric'
            ],
            'teamResults.*.team_result_users.*.score' => [
                'required_with:teamResults.*.team_result_users',
                'numeric'
            ],
            'teamResults.*.team_result_users.*.crawl_score' => [
                'required_with:teamResults.*.team_result_users',
                'numeric'
            ],
        ];
    }

    /**
     * Save event action
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function save(): void
    {
        $this->validate();

        DB::beginTransaction();

        Event::query()
            ->where('id', $this->event->id)
            ->update([
                'status' => 1,
                'comment' => $this->comment
            ]);

        foreach ($this->teamResults as $teamResult) {
            TeamResult::query()
                ->where('id', $teamResult['id'])
                ->update([
                    'score' => +$teamResult['score'],
                    'crawl_score' => +$teamResult['crawl_score']
                ]);

            foreach ($teamResult['team_result_users'] as $teamResultUser) {
                TeamResultUser::query()
                    ->where('id', $teamResultUser['id'])
                    ->update([
                        'score' => +$teamResultUser['score'],
                        'crawl_score' => +$teamResultUser['crawl_score']
                    ]);
            }
        }

        DB::commit();

        $this->saveIsSuccessful = true;

        session()->flash('status', 'Event updated');

        redirect("/events/{$this->event->id}");
    }

    /**
     * Render component
     * 
     * @return View
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function render()
    {
        return view('livewire.update-event');
    }
}

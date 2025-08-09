<?php

namespace App\Services;
use App\Models\Event;
use App\Repositories\Eventor\EventRepository;
use Illuminate\Support\Facades\Auth;

class EventService
{
    protected EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws \Exception
     */
    public function createEvent(array $data): Event
    {
        $data['user_id'] = Auth::id() ?? throw new \Exception('User must be authenticated to create an event.');
        return $this->eventRepository->create([
            'title' => $data['event_title'],
            'issuer' => $data['issuer'],
            'start_date' => $data['event_start_date'],
            'end_date' => $data['event_end_date'],
            'user_id' => $data['user_id'],
        ]);
    }
}

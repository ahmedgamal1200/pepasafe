<?php

namespace App\Repositories\Eventor;


use App\Models\Event;
use Exception;
use Illuminate\Support\Str;

class EventRepository
{
    /**
     * @throws Exception
     */
    public function create(array $data)
    {
        if (!isset($data['user_id'])) {
            throw new Exception('User ID is required to create an event.');
        }

        return Event::query()->create([
            'title' => $data['title'],
            'issuer' => $data['issuer'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'user_id' => $data['user_id'],
            'slug' => Str::slug($data['title']) . '-' . uniqid(),
        ]);
    }
}

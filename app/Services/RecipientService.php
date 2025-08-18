<?php

namespace App\Services;

use App\Repositories\Eventor\RecipientRepository;


class RecipientService
{
    protected RecipientRepository $recipientRepository;

    public function __construct(RecipientRepository $recipientRepository)
    {
        $this->recipientRepository = $recipientRepository;
    }

    public function createRecipients($recipientFile, int $eventId): void
    {
//        return $this->recipientRepository->createRecipients($recipientFile, $eventId);
        $this->recipientRepository->createRecipients($recipientFile, $eventId);
    }

    public function getRecipientCount($recipientFile): int
    {
        return $this->recipientRepository->getRecipientCount($recipientFile);
    }
}

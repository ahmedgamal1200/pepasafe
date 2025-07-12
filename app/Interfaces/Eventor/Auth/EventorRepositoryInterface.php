<?php

namespace App\Interfaces\Eventor\Auth;

use App\Http\Requests\Eventor\Auth\EventorRegisterRequest;

interface EventorRepositoryInterface
{
    public function registerEventor(EventorRegisterRequest $request);
}

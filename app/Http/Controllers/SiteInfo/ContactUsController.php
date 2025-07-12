<?php

namespace App\Http\Controllers\SiteInfo;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Models\OfficialEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function sendMessageToMail(StoreMessageRequest $request): JsonResponse
    {
        $message = "Message: {$request->message}\n";
        $message .= "Email: {$request->email}\n";
        $message .= "Name: {$request->name}\n";

        $officialEmail = OfficialEmail::query()->value('email');

        Mail::raw($message, function ($mail) use ($officialEmail ,$request) {
            $mail->to($officialEmail)
                ->from($request->email, $request->name)
                ->subject('New Contact Message from ' . ($request->name ?? ''))
                ->replyTo($request->email ?? '');
        });
        return response()->back()->with('success', 'تم ارسال رسالتك بنجاح');
    }
}

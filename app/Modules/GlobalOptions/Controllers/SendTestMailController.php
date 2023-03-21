<?php

namespace App\Modules\GlobalOptions\Controllers;

use App\Http\Controllers\MyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendTestMailController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_admin');
    }


    public function index(Request $request)
    {
        $mail = $request->mail;
        try {
            Mail::raw('Тестовое письмо', function ($message) use ($mail) {
                $message->to($mail);
                $message->subject('Тестовое письмо');
            });
            return response(true);
        } catch (\Exception $e) {
            return response(['error' => true, 'message' => $e->getMessage()], 409);
        }
        return response(true);
    }

}

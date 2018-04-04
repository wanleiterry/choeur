<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Jobs\SendShortMessage;

class TestJobsController extends Controller
{
    public function sendEmail()
    {
        echo 'test send email.';
        $this->dispatch(new SendEmail());
    }

    public function sendShortMessage()
    {
        echo 'test send short-message.';
        $this->dispatch(new SendShortMessage());
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HistoryAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $history;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($history, $user)
    {
        $this->history = $history;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Sample hospital - Your doctor's prescription")->view('historyAdded');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $date;
    public $name;
    public $slot;
    public $number;

    /**
     * Create a new message instance.
     */
    public function __construct($date, $name, $slot, $number)
    {
        $this->date = $date;
        $this->name = $name;
        $this->slot = $slot;
        $this->number = $number;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Appointment Booking Received')
                    ->view('emails.appointment_admin')
                    ->with([
                        'date' => $this->date,
                        'name' => $this->name,
                        'slot' => $this->slot,
                        'number' => $this->number
                    ]);
    }
}

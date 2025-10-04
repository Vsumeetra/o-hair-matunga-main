<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmUser extends Mailable
{
    use Queueable, SerializesModels;

    public $date;
    public $name;
    public $slot;

    /**
     * Create a new message instance.
     */
    public function __construct($date, $name, $slot)
    {
        $this->date = $date;
        $this->name = $name;
        $this->slot = $slot;
    }
    
    public function build()
    {
        return $this->subject('Your Salon Appointment is Confirmed')
                    ->view('emails.appointment_confirm_user')
                    ->with([
                        'date' => $this->date,
                        'name' => $this->name,
                        'slot'=> $this->slot,
                    ]);
    }
    
}

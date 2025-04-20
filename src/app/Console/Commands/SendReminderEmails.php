<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderMail;
use Carbon\Carbon;
use App\Models\Reservation;

class SendReminderEmails extends Command
{
    protected $signature = 'send:reminder-emails';

    protected $description = 'Send reminder emails to users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $today = Carbon::today();
        $reservations = Reservation::whereDate('reservation_date', $today)->get();

        foreach ($reservations as $reservation) {
            Mail::to($reservation->user->email)->send(new ReminderMail($reservation));
        }

        $this->info('Reminder emails sent successfully.');
    }
}

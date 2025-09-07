<?php

namespace App\Jobs\User;

use App\Mail\User\WelcomeMail;
use App\Models\User\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

/**
 * Job for sending welcome emails to new Trampala users asynchronously.
 * 
 * This queued job handles sending welcome emails to users
 * after successful registration to the Trampala listing platform.
 * The email introduces users to the platform's listing creation,
 * approval process, and community features.
 *
 * @package App\Jobs\User
 */
class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param User $user The new Trampala user to send welcome email to
     */
    public function __construct(public User $user,) {}

    /**
     * Execute the job to send welcome email.
     * 
     * Sends a welcome email to the new Trampala user explaining
     * how to create listings, the approval process, and platform features.
     * Uses the WelcomeMail mailable with platform-specific content.
     *
     * @return void
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new WelcomeMail($this->user));
    }
}

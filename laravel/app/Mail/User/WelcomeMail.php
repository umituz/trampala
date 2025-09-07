<?php

namespace App\Mail\User;

use App\Models\User\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Mailable class for sending welcome emails to new Trampala users.
 * 
 * This mailable handles the composition and sending of welcome emails
 * to users after successful registration. It provides a friendly
 * introduction to the Trampala listing platform and guides users
 * through the listing creation and approval process.
 *
 * @package App\Mail\User
 */
class WelcomeMail extends Mailable
{
    /**
     * Create a new mailable instance.
     *
     * @param User $user The new Trampala user to send the welcome email to
     */
    public function __construct(
        public User $user,
    ) {}

    /**
     * Get the message envelope configuration.
     * 
     * Configures the email sender and subject line
     * for the Trampala welcome message.
     *
     * @return Envelope Email envelope configuration
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Welcome to Trampala - Start Creating Your Listings Today!',
        );
    }

    /**
     * Get the message content definition.
     * 
     * Defines the email view template for the Trampala welcome message.
     * Includes information about listing creation and platform features.
     *
     * @return Content Email content configuration
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user.welcome',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array Array of attachments (currently empty)
     */
    public function attachments(): array
    {
        return [];
    }
}

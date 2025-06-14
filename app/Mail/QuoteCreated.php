<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $quote;
    public $paymentLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Quote $quote, $paymentLink)
    {
        $this->quote = $quote;
        $this->paymentLink = $paymentLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@zapolim.com')
                    ->replyTo('info@a2blogistiks.uk')
                    ->view('emails.quote_created')
                    ->with([
                        'quote' => $this->quote,
                        'paymentLink' => $this->paymentLink,
                    ]);
    }
}
<?php
/**
 *  @author: $rachow
 */
namespace TicketTailor\Webhook\Contracts;

interface WebhookInterface
{
    // @var - Initial webhook backoff.
    public const INITIAL_WEBHOOK_BACKOFF = 1;

    // @var - Exponential backoff retries.
    public const MAX_WEBHOOK_RETRY = 5;

    /**
     *  Execute the webhook process.
     *
     *  @return void
     *  @throws \Exception
     *          \HttpException
     *          \SocketException
     *
     */
    public function execute();

    /**
     *  Returns the running process id.
     *
     *  @return int
     *
     */
    public function getProcessId(): int;
}

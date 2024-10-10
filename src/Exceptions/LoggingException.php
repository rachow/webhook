<?php

namespace TicketTailor\Webhook\Exceptions;

use TicketTailor\Webhook\Exceptions\ExceptionHandler;

class LoggingException extends ExceptionHandler
{
    public function report()
    {
        parent::report();
    }

    public function render()
    {
        parent::render();
    }
}
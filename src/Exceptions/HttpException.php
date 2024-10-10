<?php
/**
 *  @author: $rachow
 */

namespace TicketTailor\Webhook\Exceptions;

use TicketTailor\Webhook\Exceptions\ExceptionHandler;
use TicketTailor\Webhook\Enums\HttpStatus;

class HttpException extends ExceptionHandler
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
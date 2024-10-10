<?php
/**
 *  @author: $rachow
 */

namespace TicketTailor\Webhook\Exceptions;

use \Exception;
use TicketTailor\Webhook\Exceptions\ExceptionHandler;

class SocketException extends ExceptionHandler
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
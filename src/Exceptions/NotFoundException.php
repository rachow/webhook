<?php
/**
 * @author: $rachow 
 */

 namespace TicketTailor\Webhook\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function getMessage()
    {
        return "Not Found";
    }
}
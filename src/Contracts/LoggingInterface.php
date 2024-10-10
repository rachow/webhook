<?php
/**
 *  @author: $rachow
 * 
 *  Logging Interface =>
 *      FileLoggingService | CloudLoggingService
 */
namespace TicketTailor\Webhook\Contracts;

interface LoggingInterface
{
    public function writeToLog(string $msg, ?string $filename = null);
}
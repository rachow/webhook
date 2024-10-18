<?php
/**
 *  @author: $rachow
 * 
 *  Logging Interface for
 *      FileLoggingService | CloudLoggingService
 *
 */
namespace TicketTailor\Webhook\Contracts;

interface LoggingInterface
{
    /**
     * Write the log data to service.
     */
    public function writeToLog(string $msg, ?string $filename = null);
}

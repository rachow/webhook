<?php
/**
 *  @author: $rachow
 *
 *  todo: add a cloud logging service fire + forget request.
 *        or add Message Broker to act as proxy.
 *
 */

namespace TicketTailor\Webhook\Services\Logging;

use Exception;
use TicketTailor\Webhook\Exceptions\LoggingException;
use TicketTailor\Webhook\Contracts\LoggingInterface;

class FileLoggingService implements LoggingInterface
{

    /**
     * Write to the log to filesystem.
     *
     * @param string $msg
     * @param string $filename
     *
     */
    public function writeToLog(?string $msg = '', ?string $filename = null)
    {       
        $logFile = __DIR__ . '/../../../logs/';
        $logFile .= !(is_null($filename)) ? $filename . '.log' : date('Y-m-d') . '.log'; 
                
        if (!$fh = fopen($logFile, 'a+')) {
            throw new LoggingException(sprintf('Unable to open file %s', $logFile));
        }

        fwrite($fh, sprintf('[%s]: %s', date('Y-m-d H:i:s'), $msg) . PHP_EOL);
        fclose($fh);
    }
}

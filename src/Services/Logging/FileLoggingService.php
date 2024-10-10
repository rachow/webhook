<?php
/**
 *  @author: $rachow
 *
 *  todo: Cloud Logging = ELK
 *          AMQP / Broker = Drip Feed
 *          Attach heaader, e.g.
 *          X-Request-Id: 213d350b-92d5-41a0-981d-04029c4c797e
 *
 */

namespace TicketTailor\Webhook\Services\Logging;

use Exception;
use TicketTailor\Webhook\Exceptions\LoggingException;
use TicketTailor\Webhook\Contracts\LoggingInterface;

class FileLoggingService implements LoggingInterface
{
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
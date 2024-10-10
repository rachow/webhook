<?php
/**
 *  @author: $rachow
 *
 *  Handle all exceptions.
 *
 */

namespace TicketTailor\Webhook\Exceptions;

use Throwable;
use Exception;
use ErrorException;
use TicketTailor\Webhook\Exceptions\NotFoundException;
use TicketTailor\Webhook\Exceptions\LoggingException;
use TicketTailor\Webhook\Exceptions\SocketException;
use TicketTailor\Webhook\Exceptions\HttpException;
use TicketTailor\Webhook\Services\Logging\FileLoggingService;

class ExceptionHandler extends Exception
{
    protected bool $debug = false;

    protected bool $reported = false;

    /**
     * Creates an instance.
     * 
     */
    public function __construct()
    {
        if (defined('APP_DEBUG') && APP_DEBUG == true) {
            $this->debug = true;
        }
    }

    /**
     * Handle uncaught exceptions.
     */
    public function handleException(Exception $exception)
    {
        //die(var_dump($exception));

        $this->report($exception);
        $this->render($exception);
    }
 
    /**
     * Convert errors to exceptions.
     */
    public function handleError(int $code, string $message, string $file, int $line = 0)
    {
        // any quirky stuff here? like check error_reporting(), ini config settings.
        throw new ErrorException($message, 0, $code, $file, $line);
    }

    public function report(Exception $e)
    {
        $hostname = gethostname();
        if ($this->debug && php_sapi_name() === 'cli') {
            fwrite(STDERR, 
                sprintf('Host: [%s] Error: %s', $hostname, $e->getMessage()) . PHP_EOL
            );
            $this->reported = true;
            return;
        }
    
        if ($exception instanceof NotFoundException) {
            //
        } else if ($exception instanceof HttpException) {
            $message = '::warning:: ' . $exception->getMessage();
        } else if ($exception instanceof SocketException) {
            $message = '::critical:: ' . $exception->getMessage();
            $mssage .= shell_exec('ulimit -n'); // maxed on sockets ? 
        } else if ($exception instanceof ErrorException) {
            // damm it was an error not an exception!
        }
        
        $this->sendExceptionMail($message);
    }

    public function render(Exception $e)
    {
        $hostname = gethostname();
        if ($this->debug && php_sapi_name() === 'cli' && $this->reported == false) {
            echo sprintf('Host: [%s] Error: %s', gethostname(), $e->getMessage());
            return;
        } else {
            echo ($this->reported) ?: "An Error occurred";
        }
    }

    protected function sendExceptionMail(?string $message)
    {
        /**
         * TODO: Here we call service to send E-mail or Slack Notification.
         */
    }
}
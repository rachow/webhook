<?php
/**
 *  @author: $rachow
 * 
 *  Base Command Handler.
 *  
 */

namespace TicketTailor\Webhook\Console;

abstract class Command
{
    public $signature = '';
    public $description = '';

    public function __construct()
    {
        //
    }

    // execute the command
    abstract public function execute();

    // output to console
    protected function output(string $msg)
    {
        fwrite(STDOUT, $msg . PHP_EOL);
    }
}
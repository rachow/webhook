<?php
/**
 *  @author: $rachow
 * 
 *  Command Kernel, Loads all available commands 
 *  for execution.
 *  
 */

namespace TicketTailor\Webhook\Console;

class CommandKernel
{
    protected array $commands = [];

    public function __construct()
    {
        $this->loadCommands();
    }

    protected function loadCommands()
    {
        $commandDir = __DIR__ . '/Commands';
        $commandFiles  = glob($commandDir . '/*.php');
        foreach ($commandFiles as $file) {
            $classname = 'TicketTailor\Webhook\Console\Commands\\' . basename($file, '.php');
            if (class_exists($classname)) {
                // initiate class and get signature.
                $commandObj = new $classname();
                if (property_exists($commandObj, 'signature')) {
                    $this->commands[$commandObj->signature] = $commandObj;
                }
            }
        }
    }

    public function showCommands()
    {
        echo '==== Available Commands =====' . PHP_EOL;
        foreach ($this->commands as $signature => $command) {
            echo sprintf('[%s] - %s' . PHP_EOL, $signature, $command->description);
        }
    }

    public function execute($argv)
    {
        if (count($argv) < 2) {
            echo 'No command specified.' . PHP_EOL;
            $this->showCommands();
            return;
        }

        $signature = $argv[1];

        if (!isset($this->commands[$signature])) { 
            echo sprintf('Command %s not found.' . PHP_EOL, $signature);
            $this->showCommands();
            return;
        }

        // execute the given command
        $cmd = $this->commands[$signature];
        $command = new $cmd();
        $command->execute();
    }
}
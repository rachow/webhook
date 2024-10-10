<?php
/**
 *  @author: $rachow
 * 
 *  Run Webhook Service.
 * 
 */

namespace TicketTailor\Webhook\Console\Commands;

use TicketTailor\Webhook\Console\Command;
use TicketTailor\Webhook\Services\WebhookService;

class RunWebhookCommand extends Command
{
    // @var string - callable command
    public $signature = 'run:webhook';

    // @var string - command description
    public $description = 'Run Webhook Service.';

    public function execute()
    {
        $this->output('Webhook Service Started...');
        $webhookService = new WebhookService();
        $webhookService->execute();
        $this->output('Process complete.');
    }
}
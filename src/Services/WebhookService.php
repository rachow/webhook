<?php
/**
 *  @author: $rachow
 */

namespace TicketTailor\Webhook\Services;

use Exception;
use TicketTailor\Webhook\Exceptions\LoggingException;
use TicketTailor\Webhook\Contracts\WebhookInterface;
use TicketTailor\Webhook\Services\QueueService;
use TicketTailor\Webhook\Services\HttpService;
use TicketTailor\Webhook\Services\Logging\FileLoggingService;

class WebhookService implements WebhookInterface
{
    // @var - process id of current worker.
    protected ?int $processId = null;

    // @var - queue service
    protected ?QueueService $queueService = null;

    // @var - queue name
    protected ?string $queueName = null;

    // @var - logging service
    protected ?FileLoggingService $loggingService = null;

    // @var - http service
    protected ?HttpService $httpService = null;

    /**
     * Creates an instance.
     *
     */
    public function __construct()
    {
        $this->processId = getmypid();

        $this->queueService = new QueueService();
        $this->loggingService = new FileLoggingService(); 
        $this->httpService = new HttpService();
    }

    /**
     * Run the long running service potentially.
     *
     * @return void
     *
     */
    public function execute(): void
    {
        $this->cleanupProcs();
        $this->writeToLogService('Webhook Service Started.');
        $this->ps(); // initial PID file.

        $this->writeToLogService(
            sprintf('Running process: [%s]', $this->getProcessId())
        );
    
        try {

            $this->queueName = $this->queueService->getQueueName();
            $queueData = $this->queueService->getQueueData();
            
            array_shift($queueData); // remove header
            
            $queueTotal = count($queueData);
            
            $this->writeToLogService(
                sprintf('Queue total: [%s]', $queueTotal)
            );

            for ($k=1; $k<$queueTotal; $k++) {
            
                $url = $queueData[$k][0];
            
                if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
                    $this->writeToLogService(
                        sprintf('Invalid URL: [%s] skipping.', $url)
                    );
                    continue;
                }
            
                $this->ps(); // I'm alive per Q item checkpoint.
                $this->processQueueData($url, $data = [
                    'OrderId' => $queueData[$k][1],
                    'Name' => $queueData[$k][2],
                    'Event' => $queueData[$k][3],
                ]);
            }
        } catch (Exception $e) {
            $this->writeToLogService(
                sprintf('Error: %s', $e->getMessage())
            );           
        }

        $this->writeToLogService('Process complete.');
    }

    /**
     * Process single Queue Job.
     *
     * @param string $url
     * @param ?array $data
     *
     */
    private function processQueueData(string $url, ?array $data)
    {
        $retry = 0;
        $backoff = self::INITIAL_WEBHOOK_BACKOFF;

        while ($retry < self::MAX_WEBHOOK_RETRY) {
            $response = $this->httpService->post($url, $data);
            if (isset($response['status']) && ($response['status'] >= 200 && $response['status'] <= 299 )) {
                $this->writeToLogService(sprintf('URL: %s => OK [%s]', $url, $response['status']));
                return true;
            } else {
                $retry ++;
                $this->writeToLogService(sprintf('Failed Retrying URL: %s => Status: %s', $url, $response['status'] ?? ''));
                $this->writeToLogService(sprintf('Response: %s', json_encode($response)));
                sleep($backoff);
                $backoff *= 2; // Exponential backoff.
            }    
        }
    }

    /**
     * Write to the injected logging service.
     *
     * @param ?string $msg
     *
     */
    protected function writeToLogService(?string $msg = ''): void
    {
        $this->loggingService->writeToLog(
            sprintf('[%s] %s', $this->getProcessId(), $msg),
            'webhook' // filename
        );
    }

    /**
     * Set the process id of current worker.
     *
     * touch ID to check process still running or 'ps ..au..'.
     *
     */
    protected function ps(): void
    {
        touch(__DIR__ . '/../../logs/' . $this->getProcessId() . '.pid');
    }

    /**
     * Remove all process files to start with.
     * todo: if multiple processes running tweak this.
     *
     */
    protected function cleanupProcs()
    {
        array_map('unlink', glob(__DIR__ . '/../../logs/*.pid'));
    }

    /**
     * Grab the current process ID assigned.
     *
     * @return int
     *
     */
    public function getProcessId(): int
    {
        return $this->processId;
    }
}

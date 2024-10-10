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

    // @var - logging service
    protected ?FileLoggingService $loggingService = null;

    // @var - http service
    protected ?HttpService $httpService = null;

    public function __construct()
    {
        $this->processId = getmypid();

        $this->queueService = new QueueService();
        $this->loggingService = new FileLoggingService(); 
        $this->httpService = new HttpService();
    }

    public function execute(): void
    {
        $this->cleanupProcs();
        $this->writeToLogService('Webhook Service Started.');
        $this->ps();

        $this->writeToLogService(
            sprintf('Running process: [%s]', $this->getProcessId())
        );
    
        try {
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

    protected function writeToLogService(?string $msg = ''): void
    {
        $this->loggingService->writeToLog(
            sprintf('[%s] %s', $this->getProcessId(), $msg),
            'webhook' // filename
        );

/*         try {
            // additionally add node [x] to the log to trace.
            $this->loggingService->writeToLog(
                sprintf('[%s] %s', $this->getProcessId(), $msg),
                'webhook' // filename
            );
        } catch (LoggingException $e) {
            fwrite(
                php_sapi_name() == 'cli' ? STDERR : fopen('php://stderr', 'w'),
                sprintf('Error: %s', $e->getMessage()) . PHP_EOL . $e->getMessage()
            );
        } catch (\Exception $e) {
            fwrite(
                php_sapi_name() == 'cli' ? STDERR : fopen('php://stderr', 'w'),
                sprintf('Error: %s', $e->getMessage()) . PHP_EOL . $e->getMessage()
            );
        } */
    }

    /**
     * Set the process id of current worker.
     * Keep on touching to keep me alive. | glob('/*.pid')
     */
    protected function ps(): void
    {
        touch(__DIR__ . '/../../logs/' . $this->getProcessId() . '.pid');
    }

    /**
     * Just clean all proc files.
     */
    protected function cleanupProcs()
    {
        array_map('unlink', glob(__DIR__ . '/../../logs/*.pid'));
    }
    public function getProcessId(): int
    {
        return $this->processId;
    }
}
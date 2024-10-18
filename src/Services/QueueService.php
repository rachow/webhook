<?php
/**
 *  @author: $rachow
 *
 *  - RedisQueueService = implements QueueInterface
 *  - DatabaseQueueService = implements QueueInterface
 *
 */

namespace TicketTailor\Webhook\Services;

use TicketTailor\Webhook\Contracts\QueueInterface;

class QueueService implements QueueInterface
{
    /**
     * Creates an instance.
     *
     */
    public function __construct(
        protected ?string $queueFilePath = __DIR__ . '/../../data/',
        protected ?string $queueFileName = 'webhooks.txt',
        protected ?string $queueFileType = 'csv',
    )
    {
        //
    }

    /**
     * Get the name of the Queue.
     *
     * @return string
     *
     */
    public function getQueueName(): string
    {
        return 'default';
    }

    /**
     * Get the queue data.
     * todo:
     *     - Database | Redis / Memcached = Key/Value store
     *
     * @return array
     *
     */
    public function getQueueData(): array
    {
        switch ($this->queueFileType) {
            case 'csv':
                return $this->getCsvData();
            case 'json':
                return $this->getJsonData();
            default:
                return [];
        }
    }

    /**
     * Get the CSV data.
     *      - todo: I/O resource + race conditions, free memory, locking/unlocking.
     *      - LOCK_EX, LOCK_UN
     *
     * @return array
     *
     */
    protected function getCsvData(): array
    {
        if (file_exists($this->queueFilePath . $this->queueFileName)) {
            return array_map('str_getcsv', file($this->queueFilePath . $this->queueFileName));
        }       
        return [];
    }

    /**
     * Get the JSON data.
     *
     * @return array
     *
     */
    protected function getJsonData(): array
    {
        if (file_exists($this->queueFilePath . $this->queueFileName)) {
            return json_decode(file_get_contents($this->queueFilePath . $this->queueFileName), true);
        }
        return [];
    }
}

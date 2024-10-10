<?php
/**
 *  @author: $rachow
 */

namespace TicketTailor\Webhook\Contracts;

interface QueueInterface
{
    /**
     * Gets the name of the current queue.
     * @return string
     */
    public function getQueueName(): string;

    /**
     * Returns the queue data, easily swap with database
     * or any other data source e.g. Redis/RabbitMQ
     * @return array
     */
    public function getQueueData(): array;
}
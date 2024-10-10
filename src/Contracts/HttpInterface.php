<?php
/**
 *  @author: $rachow
 */
namespace TicketTailor\Webhook\Contracts;

interface HttpInterface
{
    /**
     * Sends the HTTP GET request
     */
    public function get(string $url, array $params = []);

    /**
     * Sends the HTTP POST request
     */
    public function post(string $url, array $data = []);

    /**
     * Sends the HTTP PUT request
     */
    // public function put();
}
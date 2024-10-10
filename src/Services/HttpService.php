<?php
/**
 *  @author: $rachow
 */

namespace TicketTailor\Webhook\Services;

use TicketTailor\Webhook\Exceptions\HttpException;
use TicketTailor\Webhook\Exceptions\SocketException;
use TicketTailor\Webhook\Contracts\HttpInterface;
use TicketTailor\Webhook\Enums\HttpStatus;

class HttpService implements HttpInterface
{
    // @var - bind the socket.
    protected $socket = null;

    // @var - request string.
    protected ?string $request = null;

    /**
     * Creates an instance
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Perform a HTTP verb GET request.
     *
     * @param $url
     *
     */
    public function get(string $url, array $params = [])
    {
        $urlParts = parse_url($url);

        if (!isset($urlParts['host'])) {
            throw new HttpException(HttpStatus::NOT_FOUND);
        }
    
        $host = $urlParts['host'];
        $port = isset($urlParts['scheme']) && $urlParts['scheme'] === 'https' ? 443 : 80;
        $path = isset($urlParts['path']) ? $urlParts['path'] : '/';
        $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';

        // TODO: complete the fire request = socket open | close
    }

    /**
     * Perform a HTTP verb POST request :/JSON
     *
     * @param $url
     * @param array $data 
     */
    public function post(string $url, array $data = [])
    {
        $urlParts = parse_url($url);

        if (!isset($urlParts['host'])) {
            throw new HttpException(HttpStatus::NOT_FOUND);
        }

        $host = $urlParts['host'];
        $port = isset($urlParts['scheme']) && $urlParts['scheme'] === 'https' ? 443 : 80;
        $path = isset($urlParts['path']) ? $urlParts['path'] : '/';
        $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';

        /**
         * Curl = you need the PHP extension
         * fsockopen = simplified and vanilla
         * socket_create = low level vanilla flavour
         */

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    
        if (!$this->socket) {
            $error = socket_strerror(socket_last_error());
            throw new SocketException(
                sprintf('Socket create failed. %s', $error)
            );
            return;
        }

        $connection = socket_connect($this->socket, $host, $port);

        if (!$connection) {
            $error = socket_strerror(socket_last_error($this->socket));
            socket_close($this->socket);
            throw new SocketException(
                sprintf('Connection failed to [%s]. %s', $host, $error)
            );
            return;
        }

        // $postData = http_build_query($data);
        
        $postData = json_encode($data);
        $contentLength = strlen($postData);

        $request  = sprintf("POST %s%s HTTP/1.1\r\n", $path, $query);
        $request .= "Host: $host\r\n";
        // $request .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8 \r\n";
        $request .= "Content-Type: application/json; charset=utf-8 \r\n";
        $request .= "Content-Length: $contentLength\r\n";
        $request .= "Connection: Close\r\n";
        $request .= $postData;
    }
}
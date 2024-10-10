<?php
/**
 *  @author: $rachow
 *
 *  Common helpers.
 *
 */

if (!function_exists('send_webhook_data')) {
    /**
     * Send data to webhook endpoint.
     *  fsockopen && socket_create -> talk to OS.
     *      - Quickly open a socket on port xxx
     */
    function send_webhook_data(string $endpoint, ?array $data = null)
    {
        $urlParts = parse_url($endpoint);
        
        if (!isset($urlParts['scheme']) || !isset($urlParts['host'])) {
            write_to_logger('No scheme http(s):// or host provided.');
            return;
        }
    
        $host = $urlParts['host'];
        $port = $urlParts['port'] ?? 80;
        $path = $urlParts['path'] ?? '/';
        $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';

        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$sock) {
            $error = socket_strerror(socket_last_error());
            write_to_logger(sprintf('Socket create failed. %s', $error));
            return;
        }

        // connect now
        $connection = socket_connect($sock, $host, $port);

        if (!$connection) {
            $error = socket_strerror(socket_last_error());
            socket_close($sock); // gracefully close, OS
            write_to_logger(sprintf('Socket connection failed. %s', $error));
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

        write_to_logger($request); // offload any disk/resource consumption here!

        // smok'n -> write fast.
        socket_write($sock, $request, strlen($request));

        $response = '';
        while ($line = socket_read($sock, 1024)) {
            $response .= $line;
        }
        socket_close($sock);
    
        $httpStatusCode = 0;
        list($responseHeader, $responseBody) = explode("\r\n\r\n", $response, 2);

        preg_match("/HTTP\/\d\.\d\s(\d{3})/", $responseHeader, $matches);
        $httpStatusCode = isset($matches[1]) ? (int) $matches[1] : 0;

        return [
            'status' => $httpStatusCode,
            'header' => $responseHeader,
            'body' => $responseBody,
            'response' => $response,
        ];
    }
}

if (!function_exists('write_to_logger')) {
    /**
     * Send data to a service = ELK/Cloud or FS
     *  - TCP write fast/no waiting (Fire + Forget)
     */
    function write_to_logger($msg, $print_cli = false, $file = 'webhook.log')
    {
        $service = null; // Logstash, CloudWatch

        if ($service instanceof CloudLoggingService) {
            $service->writeToLog($msg);
            return;
        }

        if ((bool) $print && php_sapi_name() === 'cli') {
            print_error($msg);
            return;
        }

        if ($fo = fopen(__DIR__ . DS . '..' . DS . 'logs' . DS . $file, 'a')) {
            fwrite($fo, format_error_msg($msg));
            fclose($fo);
        }
    }
}

if (!function_exists('format_error_msg')) {
    function format_error_msg($msg, $context = [])
    {
        $msg = is_array($msg) ? implode(PHP_EOL, $msg) : $msg;
        $msg .= PHP_EOL;

        return sprintf('[%s] %s %s', date('Y-m-d H:i:s'), implode(PHP_EOL, $context), $msg);
    }
}

if (!function_exists('print_error')) {
    /**
     * print to standard error. Cloudwatch can read.
     */
    function print_error($msg)
    {
        // php://stderr
        fwrite(STDERR, format_error_msg($msg));
    }
}

if (!function_exists('get_queue_data')) {
    function get_queue_data($file = null)
    {
        $file = $file ?? __DIR__ . DS . '..' . DS . 'data' . DS . 'webhooks.txt';

        if (!file_exists($file)) {
            write_to_logger('[' . $file . '] does not exist.');
            return;
        }

        // return explode(',', file_get_contents($file)); 
        
        if (($fo = fopen($file, "r")) !== FALSE) { // low level and can lock!
            $return = [];
            while (($data = fgetcsv($fo, 1000, ",")) !== FALSE) {
                $return[] = $data;
            }
            fclose($fo);
            return $return;
        }
    }
}
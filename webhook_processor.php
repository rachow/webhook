#!/usr/bin/env php
<?php
/**
 *  @author: $rachow
 *   
 *  Webhook Processor => No OOP.
 * 
 *  Hook it to a scheduler / Cron = Have Fun **** /blah/blah 
 * 
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

define('MAX_WEBHOOK_RETRY', 5); // Exponential backoff.
define('INITIAL_WEBHOOK_BACKOFF', 1); // Backoff in seconds.

// pid for logging || host[x]:pid[x] -> parallel multiple worker nodes => Distributed!
$pid = getmypid();

write_to_logger('Started Webhook. [' . $pid . ']');
process_queue();

function process_queue()
{
    try {
    
        $queue = get_queue_data();
    
        array_shift($queue); // remove header index => 0
        $totalItems = count($queue);

        for ($k=1; $k<$totalItems; $k++) {
            $url = $queue[$k][0];
            if ($url) {
                process_webhook($url, $data = [
                    'OrderId' => $queue[$k][1],
                    'Name' => $queue[$k][2],
                    'Event' => $queue[$k][3],
                ]);
            }
        }
    
    } catch (Exception $e) { 
        
        // catch all exceptions if any.
        write_to_logger('Error: ' . $e->getMessage());
        print_error('Error: ' . $e->getMessage());
    
    }
}

function process_webhook($url, $data)
{
    $retryCounter = 0;
    $backoff = INITIAL_WEBHOOK_BACKOFF;

    while ($retryCounter < MAX_WEBHOOK_RETRY) {
        $response = send_webhook_data($url, $data);

        if (isset($response['status']) && ($response['status'] >= 200 && $response['status'] <= 299 )) {
            write_to_logger(sprintf('URL: %s => OK [%s]', $url, $response['status']));
            return true;
        } else {
            $retryCounter ++;
            write_to_logger(sprintf('Failed Retrying URL: %s => Status: %s', $url, $response['status']));
            write_to_logger(sprinft('Response: %s', json_encode($response)));
            sleep($backoff);
            $backoff *= 2; // Exponential backoff.
        }
    }

    // DLQ => Dead Letter Queue ??
    /**
     *  id | uuid | header | payload | error | status | created_at | retried_at 
     */

    write_to_logger(sprintf('Maximum retry reached: %s URL: %s Goodbye.', $retryCounter, $url));
}

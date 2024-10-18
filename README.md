# Webhook App

#### By Rahel Chowdhury (Engineer)

The Webhook App is a pure vanilla PHP web app that uses Exponential backoff strategy to make HTTP calls on remote endpoints that are defined within a queue process file.

Exponential backoff is great, but what if there is a significant delay in the remote  systems? Stay with me to learn more..

The setup is clean without the use of a popular framework like Laravel. Also the a pure vanilla implementation forbids the use of Curl extension or Guzzle library which are top layers of the cake.

We can tinker with the OS to handle socket creation to make TCP connections.
`fsockopen() or socket_create() - low level, fast and more control.`

## Installation

You can install the project by cloning the git repository in your preferred filesystem structure.


Ensure you have Git available and run the following.

```bash
$ git clone https://github.com/rachow/webhook.git
```

## Usage

To simply call the webhook process, you can utlise the built-in PHP development server by running the following command.

```php
$ php -S http://localhost:9090 -t public/
```

Next visit the following URL on your browser.

```
http://localhost:9090/
```

To actually run the webhook service, its a long running process, therefore you will need to trigger a CLI command to get going.

```
# Running the following PHP executable script will show all commands 
$ ./cli

No command specified.
==== Available Commands =====       
[run:webhook] - Run Webhook Service.

```

Once the Webhook Service has started, it will write a PID file in the path `~/logs/[PROC].pid`. And during each Queue item processing it will retouch this file, giving you an oppertunity to further monitor workers easily and pass to a proc monitor. => `$ ls -la | grep '.pid'`. It also allows you to locate the processes after running nohups (ofcourse you can `ps -au..`).


If there are logging taking place, feel free to tail those or even grep for errors of a particular pattern `$ cat /path/to/app/logs/webhook.log | grep 'Failed'`.
```
$ tail -f ~/logs/webhook.log

$ ls
-rw-r--r-- 3648.pid
-rw-r--r-- webhook.log

$ wc -l webhook.log
202 webhook.log
```

## Future Changes

In order to prepare for the processing of large amount of queue data, there are some changes to look into.

- Place Queue data in database or Redis (in-memory)/RabbitMQ.
   - Maintaining a structure for e.g. ``job_id | proc_id | data ..etc``
   - Status should also be recorded e.g. `pending | processing | failed | complete`
- Configure supervisor(s) which is a daemon process on node [x] and its configs to control and restart the worker process script automatically during failures. A config file will be necessary for e.g. `sudo nano /etc/supervisor/conf.d/webhook_processor.conf`

``` 
[program:webhook_processor]
command=/usr/bin/php /path/to/cli.php run:webhook
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/webhook.error.log
stdout_logfile=/var/log/supervisor/webhook.log
numprocs=1
user=www-data
```
- Add more workers to supervisord to handle gigantic queue data.

- Any logging made should be offloaded by implementing.
   - TCP Socket (Fire + Forget) = Open + Send + Close
   - Implement a Message Broker before Logging Service if needed (drip feed) based on any Throttling in place.
   - ELK or AWS (Cloudwatch) for logging.
- Consider adding Failure (Giveup) Notifcations to Slack channel `node[x] = hostname + proc_id`. Implement through creating socket (fire + forget).
- Consider building it as a core library/package to be consumed by [x] nodes across clusters, where multiple surpervisord (daemon) running to control the processes and reading from distributed Queue service.

- So what happens when the request fails after the maximum retries + exponential backoff?
  - We could push it to a DLQ (**Dead Letter Queue**).
  - DLQ would allow us to keep the requests in the event of a longer period of remote service downtime or throttle limits.
  - Send Slack notification (Webhook) to DevOps channel to alert.
  - Allow manual intervention by system admins to retry the request, or even alter request before sending.
  - DLQ can be based on a NoSQL database, MongoDB/Cassandra ...?
  - Store meaningful data e.g.
    - `id` - incremental auto generated and unique - AUTO_INCREMENT
    - `uuid` - Universal Unique Identifier, where DLQs are exposed or may form as part of the URI e.g. `/workers/ecGrs01/queue/dead/654676fe-067c-49ff-bba0-ac35ac716f13`
    - `header` - Keep the exact header that was used to send request, we may have injected e.g. `X-Request-Id: TicketTailer-Hook-001` `UserAgent: TicketTailer-Bot`. Keep as TEXT fied or JSON -> `{JSON}`.
    - `payload` - The entire request body that was sent as HTTP `POST`, `PUT`, etc.
    - `error` - Recorded error e.g. `503 Service Unavailable` - downtime/maintenance.
    - `status` - This is the DLQ status, e.g. `pending,processing,failed,complete` => ENUM type.
    - `created_at` - When the request was stored in DLQ.
    - `retried_at` - When the DLQ was retried.

## Security 
If you find any security issues with our software, then please contact our [devops](mailto:devops@localhost) team. **Please do not raise the issue on Github.**

## Contributing
Contributions are only made internally.

## License

[MIT](https://choosealicense.com/licenses/mit/)

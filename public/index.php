<?php
/**
 * @author: $rachow
 */

use TicketTailor\Webhook\Services\WebhookService;

define('DS', DIRECTORY_SEPARATOR);
require __DIR__ . DS . '..' . DS . 'bootstrap.php';

$uri = $_SERVER['REQUEST_URI'];
$uriParts = explode('?', $uri, 2);

if (isset($uriParts[0])) {
    $segments = explode('/', $uriParts[0]);
    $segments[0] = '/';
}

if (isset($uriParts[1])) {
    $query = explode('&', $uriParts[1]);
}

if ($segments[1] == 'webhook') {

    // long running process should run through CLI.
}
?>
<!DOCTYPE html>
<html lang="en">
<head id="wps-header-001">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="webmaster@localhost">
  <meta name="robots" content="noindex,nofollow">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <title>It Work&apos;s | Go Webhooks !</title>
</head>
<body id="webhook-app" bgcolor="#ffffff" onLoad="fnLoadedHooks()">
<pre>
 ______  __            __       __                      __        __               __ 
/      |/  |          /  |  _  /  |                    /  |      /  |             /  |
$$$$$$/_$$ |_         $$ | / \ $$ |  ______    ______  $$ |   __ $$/_______       $$ |
  $$ |/ $$   |        $$ |/$  \$$ | /      \  /      \ $$ |  /  |$//       |      $$ |
  $$ |$$$$$$/         $$ /$$$  $$ |/$$$$$$  |/$$$$$$  |$$ |_/$$/  /$$$$$$$/       $$ |
  $$ |  $$ | __       $$ $$/$$ $$ |$$ |  $$ |$$ |  $$/ $$   $$<   $$      \       $$/ 
 _$$ |_ $$ |/  |      $$$$/  $$$$ |$$ \__$$ |$$ |      $$$$$$  \   $$$$$$  |       __ 
/ $$   |$$  $$/       $$$/    $$$ |$$    $$/ $$ |      $$ | $$  | /     $$/       /  |
$$$$$$/  $$$$/        $$/      $$/  $$$$$$/  $$/       $$/   $$/  $$$$$$$/        $$/ 

			Time to get cracking.

</pre>
<script type="text/javascript">
	function fnProcessInit()
	{
		console.log("Hello, Thanks for popping by.");
	}
</script>
</body>
</html>
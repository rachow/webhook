<?php
/**
 *  @author: $rachow 
 */
namespace TicketTailor\Webhook\Enums;

use Traits\EnumsToArray;
use Traits\HttpStatusTrait;

enum HttpStatus: string 
{
    use EnumsToArray, HttpStatusTrait;

    case OK ='OK';   // Acknowledged
    case CREATED ='Created';   // Nice, meaning it created it!
    case ACCEPTED ='Accepted';
    case NO_CONTENT ='No Content';
    
    case MOVED_PERMANENTLY = 'Moved Permanently';
    case FOUND = 'Found';

    case BAD_REQUEST= 'Bad Request';
    case UNAUTHORIZED = 'Unauthorized';
    case PAYMENT_REQUIRED = 'Payment Required';
    case FORBIDDEN = 'Forbidden';
    case NO_FOUND = 'Not Found';
    case UNPROCESSABLE_CONTENT = 'Unprocessable Content';
    case TOO_MANY_REQUEST = 'Too Many Requests';

    case INTERNAL_SERVER_ERROR = 'Internal Server Error'; // !! WHOHANG !! Oops
    case NOT_IMPLEMENTED = 'Not Implemented';
    case BAD_GATEWAY = 'Bad Gateway';           // proxy/upstream issues ?
    case SERVICE_UNAVAILABLE = 'Service Unavailable';   // under maintenance ? too many reqs to handle ?
    case GATEWAY_TIMEOUT = 'Gateway Timeout';       // proxy => invalid upstream data received ..?
}
<?php
namespace App\Middleware;

use App\Core\Request;

class LoggerMiddleware extends Middleware 
{
    private string $logfile = '/var/www/html/logv.json';

    public function handle(Request $request, callable $next)
    {
        $this->logRequest($request);
        $response = $next($request);
        return $response;
    }

    private function logRequest(Request $request): void
    {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method'    => $request->getMethod(),
            'uri'       => $request->getUri(),
            'session_id'=> session_id ?? 'none',
        ];
        $this->writeLog($log);
    }
    

}
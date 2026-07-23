<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class LoggerMiddleware extends Middleware 
{
    private string $logFile = '/var/www/html/logs.json';

    public function __construct(
//        private Session $session
    )
    {}
    public function handle(Request $request, callable $next)
    {
        $this->logRequest($request);
        return $next($request);
    }

    private function logRequest(Request $request): void
    {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method'    => $request->getMethod(),
            'uri'       => $request->getUri(),
            'session_id'=> session_id() ?: 'none',
        ];
        $this->writeLog($log);
    }

    private function writeLog(array $data): void
    {
        $logs = [];
        if(file_exists($this->logFile)){
            $content = file_get_contents($this->logFile);
            $logs = json_decode($content, true) ?? [];
        }
        $logs[]=$data;

        file_put_contents(
            $this->logFile,
            json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
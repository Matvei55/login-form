<?php
namespace App\Core;

class HttpClient
{
    private int $timeout = 120;
    private ?array $proxyConfig = null;
    public function __construct(?array $proxyConfig = null)
    {
        $this->proxyConfig = $proxyConfig;
    }
    public function get(string $url, array $params = []): array
    {
        $fullUrl = $url . '?' . http_build_query($params); //полный урл
        return $this->request('GET', $fullUrl);
    }

    public function post(string $url, array $data = []): array //вызов метода реквест с методом пост
    {
        return $this->request('POST', $url, $data);
    }

    private function request(string $method, string $url, array $data = []): array
    {
        $ch = curl_init(); //сессия cURL
        curl_setopt($ch, CURLOPT_URL, $url); //куда отправить запрос
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //вернуть ответ как строку
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); //таймаут в секундах

        if($this->proxyConfig !== null && ($this->proxyConfig['enabled'] ?? true)) {
            $proxyHost = $this->proxyConfig['host'] ?? '127.0.0.1';
            $proxyPort = $this->proxyConfig['port'] ?? 1080;
            $proxyType = $this->proxyConfig['type'] ?? 'socks5';

            $proxyUrl = "{$proxyType}://{$proxyHost}:{$proxyPort}";
            curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);

            if($proxyType === 'socks5') {
                curl_setopt($ch, CURLOPT_PROXYPORT, CURLPROXY_SOCKS5);
            }elseif($proxyType === 'socks4') {
                curl_setopt($ch, CURLOPT_PROXYPORT, CURLPROXY_SOCKS4);
            }
        }

        if($method === 'POST'){ //если метод пост ,то настраиваем под него
            curl_setopt($ch, CURLOPT_POST, true); //вкл пост метода
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //тело запроса
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); //заголовки и шлем json
        }
        $response = curl_exec($ch); //ответ от сервера
        $error = curl_error($ch);//ошибки если есть
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);//http-status

//        curl_close($ch);
        if($error){
            throw new \Exception("Http error: $error");
        }
        $decoded = json_decode($response, true); //превращаю json строку в массив php
        if($httpCode >= 400){
            throw new \Exception("Http $httpCode:" . ($decoded['description'] ?? $response));
        }
        return $decoded;
    }
}
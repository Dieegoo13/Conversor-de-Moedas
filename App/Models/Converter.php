<?php

namespace App\Models;

use App\Config;


class Converter
{

    private $apiUrl;
    private $cacheDir;

    public function __construct()
    {
        $this->apiUrl = Config::API_URL;
        $this->cacheDir = dirname(__DIR__, 2) . 'cache';

        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function convert($from, $to, $amount)
    {

        try {

            $par = strtoupper($from) . '-' . strtoupper($to);
            $rate = $this->getExchangeRate($par);

            if ($rate === false) {

                throw new \Exception("Não foi possivel obter a taxa de câmbio");
            }

            $result = $amount * $rate['value'];

            return [
                'success' => true,
                'from' => strtoupper($from),
                'to' => strtoupper($to),
                'amount' => $amount,
                'rate' => $rate['value'],
                'result' => $result,
                'formatted_result' => $this->formatCurrency($result, $to),
                'timestamp' => $rate['timestamp'],
                'high' => $rate['high'],
                'low' => $rate['low'],
                'variation' => $rate['variation']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function getExchangeRate($pair)
    {
        $cacheFile = $this->cacheDir . md5($pair) . '.json';

        // Verifica se existe cache válido
        if (file_exists($cacheFile)) {
            $cacheTime = filemtime($cacheFile);
            if ((time() - $cacheTime) < Config::CACHE_TIME) {
                $data = json_decode(file_get_contents($cacheFile), true);
                return $data;
            }
        }

        // Busca da API
        $url = $this->apiUrl . $pair;

        $context = stream_context_create([
            'http' => [
                'timeout' => Config::API_TIMEOUT,
                'user_agent' => 'ConversorMoedas/1.0'
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            // Tenta usar cache expirado se a API falhar
            if (file_exists($cacheFile)) {
                return json_decode(file_get_contents($cacheFile), true);
            }
            return false;
        }

        $data = json_decode($response, true);
        $pairKey = str_replace('-', '', $pair);

        if (!isset($data[$pairKey])) {
            return false;
        }

        $rateData = [
            'value' => floatval($data[$pairKey]['bid']),
            'timestamp' => $data[$pairKey]['timestamp'],
            'high' => floatval($data[$pairKey]['high']),
            'low' => floatval($data[$pairKey]['low']),
            'variation' => $data[$pairKey]['pctChange']
        ];

        // Salva no cache
        file_put_contents($cacheFile, json_encode($rateData));

        return $rateData;
    }

    public function formatCurrency($valor, $moeda)
    {

        $decimals = ($moeda === 'JPY') ? 0 : 2;
        return number_format($valor, $decimals, ',', '.');
    }

    public function getAvailableCurrencies()
    {
        return Config::getCurrencies();
    }

    public function isValidPair($from, $to)
    {
        $moedas = Config::getCurrencies();
        return isset($moedas[$from]) && isset($moedas[$to]);
    }
}

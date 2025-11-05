<?php

namespace App;

class Config
{
    // URL base da aplicação
    const BASE_URL = 'http://localhost:8000';
    
    // Configurações da API
    const API_URL = 'https://economia.awesomeapi.com.br/json/last/';
    const API_TIMEOUT = 10; // segundos
    
    // Cache de cotações (em segundos)
    const CACHE_TIME = 300; // 5 minutos
    const CACHE_ENABLED = true; // Habilitar/desabilitar cache
    
    // Moedas disponíveis
    const CURRENCIES = [
        'USD' => 'Dólar Americano',
        'EUR' => 'Euro',
        'BRL' => 'Real Brasileiro',
        'GBP' => 'Libra Esterlina',
        'JPY' => 'Iene Japonês'
    ];
    
    // Ambiente (development ou production)
    const ENVIRONMENT = 'development';
    
    // Exibir erros (true apenas em desenvolvimento)
    const SHOW_ERRORS = true;
    
    // Timezone
    const TIMEZONE = 'America/Sao_Paulo';
    
    /**
     * Inicializa configurações globais
     */
    public static function init()
    {
        // Define timezone
        date_default_timezone_set(self::TIMEZONE);
        
        // Configuração de erros
        if (self::ENVIRONMENT === 'development' && self::SHOW_ERRORS) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        // Headers de segurança
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
    }
    
    /**
     * Retorna URL base
     */
    public static function getBaseUrl()
    {
        return self::BASE_URL;
    }
    
    /**
     * Retorna URL da API
     */
    public static function getApiUrl()
    {
        return self::API_URL;
    }
    
    /**
     * Retorna timeout da API
     */
    public static function getApiTimeout()
    {
        return self::API_TIMEOUT;
    }
    
    /**
     * Retorna tempo de cache em segundos
     */
    public static function getCacheTime()
    {
        return self::CACHE_TIME;
    }
    
    /**
     * Verifica se cache está habilitado
     */
    public static function isCacheEnabled()
    {
        return self::CACHE_ENABLED;
    }
    
    /**
     * Retorna moedas disponíveis
     */
    public static function getCurrencies()
    {
        return self::CURRENCIES;
    }
    
    /**
     * Verifica se está em ambiente de desenvolvimento
     */
    public static function isDevelopment()
    {
        return self::ENVIRONMENT === 'development';
    }
    
    /**
     * Verifica se está em ambiente de produção
     */
    public static function isProduction()
    {
        return self::ENVIRONMENT === 'production';
    }
}
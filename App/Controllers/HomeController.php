<?php

namespace App\Controllers;

use App\Core\Action;
use App\Models\Converter;

class HomeController extends Action
{
    private $converter;
    protected $view;

    public function __construct()
    {
        $this->view = new \stdClass();
        $this->converter = new Converter();
    }

    /**
     * Página inicial
     */
    public function index()
    {
        $currencies = $this->converter->getAvailableCurrencies();
        
        $this->render('index', [
            'currencies' => $currencies,
        ]);
    }

    /**
     * Processa conversão de moedas (via AJAX)
     */
    public function convert()
    {
        // Valida método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json([
                'success' => false, 
                'error' => 'Método não permitido'
            ], 405);
        }

        // Coleta e sanitiza dados (SEUS NAMES: amount, from, to)
        $from = $this->sanitize($this->getPost('from'));
        $to = $this->sanitize($this->getPost('to'));
        $amount = $this->getPost('amount');

        // Validações
        $validation = $this->validate($from, $to, $amount);
        
        if (!$validation['valid']) {
            $this->json([
                'success' => false,
                'error' => $validation['error']
            ], 400);
        }

        // Converte para float
        $amount = floatval(str_replace(',', '.', $amount));

        // Realiza conversão
        $result = $this->converter->convert($from, $to, $amount);

        // Retorna resultado JSON
        $statusCode = $result['success'] ? 200 : 400;
        $this->json($result, $statusCode);
    }

    /**
     * Valida dados de entrada
     */
    private function validate($from, $to, $amount)
    {
        // Valida campos obrigatórios
        if (empty($from) || empty($to)) {
            return [
                'valid' => false,
                'error' => 'Moedas de origem e destino são obrigatórias'
            ];
        }

        // Valida se são moedas diferentes
        if ($from === $to) {
            return [
                'valid' => false,
                'error' => 'Selecione moedas diferentes para conversão'
            ];
        }

        // Valida se o par é suportado
        if (!$this->converter->isValidPair($from, $to)) {
            return [
                'valid' => false,
                'error' => 'Par de moedas não suportado'
            ];
        }

        // Valida valor
        if (empty($amount) || !is_numeric(str_replace(',', '.', $amount))) {
            return [
                'valid' => false,
                'error' => 'Valor inválido'
            ];
        }

        if (floatval($amount) <= 0) {
            return [
                'valid' => false,
                'error' => 'O valor deve ser maior que zero'
            ];
        }

        // Valida valor mínimo
        if ($amount < 0.01) {
            $this->json([
                'success' => false,
                'error' => 'O valor mínimo é 0.01'
            ], 400);
        }

        // Valida valor máximo (previne overflow)
        if ($amount > 999999999) {
            $this->json([
                'success' => false,
                'error' => 'Valor muito alto para conversão'
            ], 400);
        }


        return ['valid' => true];
    }

    /**
     * Limpa a entrada retirando itens desnecessários
     */
    private function sanitize($value)
    {
        return strtoupper(trim(strip_tags($value)));
    }
}
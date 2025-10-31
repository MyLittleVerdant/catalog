<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * Сервис для отправки SMS через SMSPilot API
 */
class SmspilotService extends Component
{
    /** @var string API ключ SMSPilot */
    public $apiKey;

    /** @var string URL API SMSPilot */
    public $apiUrl = 'https://smspilot.ru/api.php';

    /** @var string Имя отправителя (откуда) */
    public $from = 'BOOKS';

    /**
     * Отправляет SMS сообщение
     * 
     * @param string $phone Номер телефона получателя (формат: 79991234567 или +79991234567)
     * @param string $message Текст сообщения
     * @return array Результат отправки
     * @throws Exception
     */
    public function sendSms(string $phone, string $message): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('SMSPilot API key is not configured');
        }

        // Нормализуем номер телефона
        $phone = $this->normalizePhone($phone);

        try {
            // Используем встроенный HTTP клиент Yii2 или file_get_contents
            $params = http_build_query([
                'send' => $message,
                'to' => $phone,
                'from' => $this->from,
                'apikey' => $this->apiKey,
                'format' => 'json',
            ]);
            
            $url = $this->apiUrl . '?' . $params;
            $responseContent = @file_get_contents($url);
            
            if ($responseContent === false) {
                throw new Exception('Failed to send request to SMSPilot API');
            }
            
            $result = json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from SMSPilot API');
            }
            
            // Проверяем результат
            if (isset($result['error'])) {
                Yii::error("SMSPilot error: {$result['error']} for phone {$phone}", 'sms');
                return [
                    'success' => false,
                    'error' => $result['error'],
                ];
            }

            if (isset($result['server_id'])) {
                Yii::info("SMS sent successfully to {$phone}, server_id: {$result['server_id']}", 'sms');
                return [
                    'success' => true,
                    'server_id' => $result['server_id'],
                ];
            }

            Yii::error("SMSPilot API unexpected response for phone {$phone}", 'sms');
            return [
                'success' => false,
                'error' => 'Unexpected API response',
            ];

        } catch (\Exception $e) {
            Yii::error("SMSPilot exception: {$e->getMessage()}", 'sms');
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Нормализует номер телефона для SMSPilot
     * Убирает все символы кроме цифр, добавляет 7 в начало если начинается с 8
     * 
     * @param string $phone Исходный номер
     * @return string Нормализованный номер
     */
    protected function normalizePhone(string $phone): string
    {
        // Убираем все символы кроме цифр
        $phone = preg_replace('/\D/', '', $phone);

        // Если номер начинается с 8, заменяем на 7
        if ($phone[0] === '8') {
            $phone = '7' . substr($phone, 1);
        }

        // Если номер не начинается с 7, добавляем 7
        if ($phone[0] !== '7') {
            $phone = '7' . $phone;
        }

        return $phone;
    }
}


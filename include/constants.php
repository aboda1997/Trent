<?php
// Constants.php

class AppConstants
{
    // Core Constants
    const Property_Count = 5;

    // Payment Methods Configuration
    const PAYMENT_METHODS = [

        'wallet' => [
            'key' => 'MWALLET',
            'name' => ['en' => 'E-Wallet', 'ar' => 'المحفظة الإلكترونية'],
        ],
        'card' => [
            'key' => 'CARD',
            'name' => ['en' => 'Card', 'ar' => 'البطاقة'],
        ],
        'fawry' => [
            'key' => 'PayAtFawry',
            'name' => ['en' => 'Fawry Pay', 'ar' => 'فوري باي'],
        ],
        'trent' => [
            'key' => 'TRENT_BALANCE',
            'name' => ['en' => 'Trent Balance', 'ar' => 'رصيد ترينت'],
        ]
    ];


    /**
     * Get all payment method validation keys
     * @return array [method => key]
     */
    public static function getAllMethodKeys()
    {
        $keys = [];
        foreach (self::PAYMENT_METHODS as $method => $config) {
            $keys[$method] = $config['key'];
        }
        return $keys;
    }

    /**
     * Get full payment methods data
     * @param string $lang Language code
     * @return array Structured method data
     */
    public static function getPaymentMethods($lang = 'ar')
    {
        $methods = [];
        foreach (self::PAYMENT_METHODS as $code => $config) {
            $methods[] = [
                'name' => $config['name'][$lang],
                'key' => $config['key'],
            ];
        }
        return $methods;
    }

    public static function getPaymentMethod( $key , $lang = 'ar')
    {
        $method = '';
        foreach (self::PAYMENT_METHODS as $code => $config) {
            if ($config['key'] == $key) {
                $method=  $config['name'][$lang];
                
            }
        }
        return $method;
    }
}

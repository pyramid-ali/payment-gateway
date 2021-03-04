<?php


namespace Alish\PaymentGateway\Utils;


use Alish\PaymentGateway\Exceptions\ConfigNotFoundException;

trait HasConfig
{
    protected $config;

    public function getConfig(string $key, $defaultValue = null, bool $strict = true)
    {
        $keys = explode('.', $key);
        $config = null;

        foreach ($keys as $configKey) {
            if (isset($this->config[$configKey])) {
                $config = $this->config[$configKey];
                continue;
            }

            if ($defaultValue || !$strict) {
                return $defaultValue;
            }

            throw new ConfigNotFoundException($key);
        }

        return $config;
    }
}
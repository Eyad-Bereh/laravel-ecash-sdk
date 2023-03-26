<?php

namespace IXCoders\LaravelEcash\Utilities;

class RequestDataTransformer
{
    public function transformDataArrayFromRequest(array $data): array
    {
        $map = [
            'IsSuccess' => 'is_successful',
            'Message' => 'message',
            'TransactionNo' => 'transaction_number',
            'Token' => 'token',
        ];

        $keys = array_keys($map);
        $values = array_values($map);
        $length = count($map);

        for ($i = 0; $i < $length; $i++) {
            $key = $keys[$i];
            $value = $values[$i];

            if (array_key_exists($key, $data)) {
                $data[$value] = $data[$key];
                unset($data[$key]);
            }
        }

        $data['is_successful'] = filter_var($data['is_successful'], FILTER_VALIDATE_BOOLEAN);

        return $data;
    }
}

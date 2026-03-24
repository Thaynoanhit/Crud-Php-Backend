<?php

namespace App\Helpers;

class ResponseHelper {

    public static function success($data = null, $message = "", $status = 200) {

        http_response_code($status);

        echo json_encode([
            "success" => true,
            "message" => $message,
            "data" => $data,
            "error" => null
        ]);
    }

    public static function error($message = "", $status = 400) {

        http_response_code($status);

        echo json_encode([
            "success" => false,
            "message" => $message,
            "data" => null,
            "error" => $message
        ]);
    }
}
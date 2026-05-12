<?php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode(['error' => $errstr]);
    exit;
});

set_exception_handler(function($exception) {
    http_response_code(500);
    echo json_encode(['error' => $exception->getMessage()]);
    exit;
});

ini_set('display_errors', 0);
error_reporting(E_ALL);
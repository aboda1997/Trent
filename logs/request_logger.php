<?php
// request_logger.php

class RequestLogger
{
    const MAX_LOG_SIZE = 1048576; // 1MB in bytes
    const MAX_LOG_FILES = 10;
    const LOG_DIR = __DIR__ . '/logs';
    const LOG_FILE = 'requests.log';
    const ARCHIVE_PREFIX = 'requests-';
    const RETENTION_DAYS = 60; // 2 months retention

    private static $sensitiveFields = [
        'password',
        'cvv',
        'token',
        'api_key',
        'secret',
        'username'
    ];

    public static function init()
    {
        self::ensureLogDirectory();
        self::rotateLogsIfNeeded();
        self::cleanupOldArchives(); // Now called on every init to ensure cleanup
        self::logRequest();
    }

    protected static function getCairoDateTime()
    {
        static $cairoTz = null;
        if ($cairoTz === null) {
            $cairoTz = new DateTimeZone('Africa/Cairo');
        }
        return new DateTime('now', $cairoTz);
    }

    protected static function logRequest()
    {
        $now = self::getCairoDateTime();

        try {
            $logData = [
                'timestamp' => $now->format('Y-m-d H:i:s'),
                'method' => $_SERVER['REQUEST_METHOD'],
                'uri' => $_SERVER['REQUEST_URI'],
                'headers' => self::filterSensitiveData(self::getAllHeaders()),
                'body' => self::filterSensitiveData(self::getRequestBody()),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'response_status' => http_response_code(),
                'response_body' => self::filterSensitiveData(self::getResponseBody()) // Add this line

            ];

            $logEntry = json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

            file_put_contents(
                self::LOG_DIR . '/' . self::LOG_FILE,
                $logEntry,
                FILE_APPEND | LOCK_EX
            );
        } catch (Exception $e) {
            error_log("RequestLogger Error: " . $e->getMessage());
        }
    }

    protected static function rotateLogsIfNeeded()
    {
        $logPath = self::LOG_DIR . '/' . self::LOG_FILE;

        if (!file_exists($logPath)) {
            return;
        }

        if (filesize($logPath) >= self::MAX_LOG_SIZE) {
            self::archiveCurrentLog();
        }
    }

    protected static function archiveCurrentLog()
    {
        $logPath = self::LOG_DIR . '/' . self::LOG_FILE;
        $now = self::getCairoDateTime();

        $archivePath = self::LOG_DIR . '/' . self::ARCHIVE_PREFIX . $now->format('Y-m-d-His') . '.log';

        if (file_exists($logPath)) {
            rename($logPath, $archivePath);
        }
    }

    protected static function cleanupOldArchives()
    {
        $files = glob(self::LOG_DIR . '/' . self::ARCHIVE_PREFIX . '*.log');
        $now = self::getCairoDateTime();
        $retentionDate = $now->modify('-' . self::RETENTION_DAYS . ' days');

        foreach ($files as $file) {
            // Extract date from filename (format: requests-Y-m-d-His.log)
            $filename = basename($file);
            $dateStr = substr($filename, strlen(self::ARCHIVE_PREFIX), -4); // Remove prefix and .log

            try {
                $fileDate = DateTime::createFromFormat('Y-m-d-His', $dateStr, new DateTimeZone('Africa/Cairo'));

                if ($fileDate && $fileDate < $retentionDate) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            } catch (Exception $e) {
                error_log("RequestLogger cleanup error for file {$file}: " . $e->getMessage());
                continue;
            }
        }
    }

    protected static function ensureLogDirectory()
    {
        if (!file_exists(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0755, true);
        }
    }

    protected static function getAllHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    protected static function getRequestBody()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            return $input ? json_decode($input, true) : [];
        }

        if (!empty($_POST)) {
            return $_POST;
        }

        $input = file_get_contents('php://input');
        if ($input) {
            parse_str($input, $parsedInput);
            return $parsedInput ?: [];
        }
        return [];
    }

    protected static function filterSensitiveData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        array_walk_recursive($data, function (&$value, $key) {
            foreach (self::$sensitiveFields as $field) {
                if (stristr($key, $field) !== false) {
                    $value = '*****';
                    break;
                }
            }
        });

        return $data;
    }
    private static function getResponseBody()
    {
        // Force buffer creation if none exists
        if (ob_get_level() == 0) {
            ob_start();
        }

        $response = ob_get_contents();

        if ($response && json_decode($response)) {
            $statusCode = http_response_code();

            // Only log for 400 and 500 status codes
            if ($statusCode === 400 || $statusCode === 500) {
                return json_decode($response, true); // Converts to array (optional)

            }
        }

        return  "";
    }
}

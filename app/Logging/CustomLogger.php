<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;
use Carbon\Carbon;

class CustomLogger
{
    /**
     * Create a custom Monolog instance for process logs.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function processLogger(array $config)
    {
        return $this->createLogger('process_log');
    }

    /**
     * Create a custom Monolog instance for error logs.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function errorLogger(array $config)
    {
        return $this->createLogger('error_log');
    }

    /**
     * Create a custom Monolog instance.
     *
     * @param  string  $logType
     * @return \Monolog\Logger
     */
    protected function createLogger($logType)
    {
        // Set the timezone to Indian Standard Time (IST)
        date_default_timezone_set('Asia/Kolkata');

        // Get current date and hour
        $currentDate = Carbon::now()->format('d-m-Y');
        $currentHour = Carbon::now()->hour;

        try {
            // Create the directory for the current date if it doesn't exist
            $logDirectory = storage_path("logs/{$logType}/{$currentDate}");
            if (!file_exists($logDirectory) && !mkdir($logDirectory, 0755, true)) {
                throw new \RuntimeException("Failed to create directory: $logDirectory");
            }

            // Create a new logger instance
            $logger = new Logger($logType);

            // Set the log file path with date and hour format
            $logFilePath = "{$logDirectory}/{$currentHour}-" . ($currentHour + 1) . '.txt';

            // Create a new stream handler to log to the file
            $handler = new StreamHandler($logFilePath, LogLevel::DEBUG);

            // Add the handler to the logger
            $logger->pushHandler($handler);

            return $logger;
        } catch (\Exception $e) {
            // Log error if logger creation fails
            $errorMessage = "Failed to create logger: {$e->getMessage()}";
            error_log($errorMessage);
            throw new \RuntimeException($errorMessage);
        }
    }
}

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler as MonologStreamHandler;

class EventLogger {
    protected $logger;
    const LOG_DESTINATION = '/logs/logs.txt';

    public function __construct($name = 'event_logger') {
        $this->logger = new MonologLogger($name);
        $this->logger->pushHandler(new MonologStreamHandler (
            $_SERVER['DOCUMENT_ROOT'] . self::LOG_DESTINATION, 
            MonologLogger::INFO
        ));
    }

    public function log($message) {
        $this->logger->info($message);
    }
}




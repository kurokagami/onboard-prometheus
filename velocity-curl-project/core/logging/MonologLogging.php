<?php
namespace App\Framework\Logging;
use App\Framework\Logging\ILogging;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\HandlerInterface;

class MonologLogging implements ILogging
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger('app_logger');
        if(trim($_ENV["ENVIRONMENT"]) == "DEVELOPMENT"){
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
        }
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    public function pushHandler(HandlerInterface $handler): void
    {
        $this->logger->pushHandler($handler);
    }
}

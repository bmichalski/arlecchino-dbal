<?php

namespace Arlekin\Dbal\Driver\Pdo\MySql\Log;

class JsonFileAppendQueryLogger implements QueryLoggerInterface
{
    /**
     * @var string
     */
    protected $file;
    
    /**
     * @var resource
     */
    protected $handle;

    /**
     * Constructor.
     * 
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function log($query, array $parameters, $start, $end, $logPayload)
    {
        $this->handle = fopen($this->file, 'a');
        
        $logEntry = new LogEntry($query, $parameters, $start, $end, $logPayload);
        
        fwrite(
            $this->handle,
            json_encode(
                $logEntry->toArray()
            )
        );
    }

    public function end()
    {
        if (null !== $this->handle) {
            fclose($this->handle);
        }
    }
}
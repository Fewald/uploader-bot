<?php

namespace Queue;

abstract class AbstractQueue
{
    /** @var  \GearmanClient */
    protected $client;

    /**
     * AbstractQueue constructor.
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    protected function doAsync($queueName, $content)
    {
        $this->client->doBackground($queueName, $content);
    }

    abstract function getName(): string;

    abstract function push(Message $message);
}
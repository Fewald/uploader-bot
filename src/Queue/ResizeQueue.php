<?php


namespace Queue;


class ResizeQueue extends AbstractQueue
{
    function getName(): string
    {
        return 'resize';
    }

    function push(Message $message)
    {
        $this->doAsync($this->getName(), $message->getContent());
    }
}
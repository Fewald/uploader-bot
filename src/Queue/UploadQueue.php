<?php


namespace Queue;


class UploadQueue extends AbstractQueue
{
    function getName(): string
    {
        return 'upload';
    }

    function push(Message $message)
    {
        $this->doAsync($this->getName(), $message->getContent());
    }
}
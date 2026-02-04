<?php

declare(strict_types=1);

namespace App\Neuron\History;

use NeuronAI\Chat\History\AbstractChatHistory;
use NeuronAI\Chat\History\ChatHistoryInterface;

class ArrayChatHistory extends AbstractChatHistory
{
    /**
     * @param \NeuronAI\Chat\Messages\Message[] $messages
     */
    public function setMessages(array $messages): ChatHistoryInterface
    {
        $this->history = $messages;
        return $this;
    }

    protected function clear(): ChatHistoryInterface
    {
        $this->history = [];
        return $this;
    }
}

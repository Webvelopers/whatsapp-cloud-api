<?php

namespace Webvelopers\WhatsAppCloudApi\Message;

use Webvelopers\WhatsAppCloudApi\Message\OptionsList\Action;

/**
 * 
 */
final class OptionsListMessage extends Message
{
    /**
     * 
     */
    protected string $type = 'list';

    /**
     * 
     */
    private string $header;

    /**
     * 
     */
    private string $body;

    /**
     * 
     */
    private string $footer;

    /**
     * 
     */
    private Action $action;

    /**
     * 
     */
    public function __construct(string $to, string $header, string $body, string $footer, Action $action)
    {
        $this->header = $header;
        $this->body = $body;
        $this->footer = $footer;
        $this->action = $action;

        parent::__construct($to);
    }

    /**
     * 
     */
    public function header(): string
    {
        return $this->header;
    }

    /**
     * 
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * 
     */
    public function footer(): string
    {
        return $this->footer;
    }

    /**
     * 
     */
    public function action(): array
    {
        return ['button' => $this->action->button(), 'sections' => $this->action->sections()];
    }
}

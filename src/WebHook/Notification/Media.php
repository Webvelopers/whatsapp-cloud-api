<?php

namespace Webvelopers\WhatsAppCloudApi\WebHook\Notification;

use Webvelopers\WhatsAppCloudApi\WebHook\Notification\Support\Business;

/**
 * 
 */
final class Media extends MessageNotification
{
    /**
     * 
     */
    private string $image_id;

    /**
     * 
     */
    private string $mime_type;

    /**
     * 
     */
    private string $caption;

    /**
     * 
     */
    public function __construct(
        string $id,
        Business $business,
        string $image_id,
        string $mime_type,
        string $caption,
        string $received_at_timestamp
    ) {
        parent::__construct($id, $business, $received_at_timestamp);

        $this->image_id = $image_id;
        $this->mime_type = $mime_type;
        $this->caption = $caption;
    }

    /**
     * 
     */
    public function imageId(): string
    {
        return $this->image_id;
    }

    /**
     * 
     */
    public function mimeType(): string
    {
        return $this->mime_type;
    }

    /**
     * 
     */
    public function caption(): string
    {
        return $this->caption;
    }
}

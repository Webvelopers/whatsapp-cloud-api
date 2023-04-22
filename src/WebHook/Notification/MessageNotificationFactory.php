<?php

namespace Webvelopers\WhatsAppCloudApi\WebHook\Notification;

use Webvelopers\WhatsAppCloudApi\WebHook\Notification\Support\Business;
use Webvelopers\WhatsAppCloudApi\WebHook\Notification\Support\Context;
use Webvelopers\WhatsAppCloudApi\WebHook\Notification\Support\Customer;
use Webvelopers\WhatsAppCloudApi\WebHook\Notification\Support\Referral;
use Webvelopers\WhatsAppCloudApi\WebHook\Notification\Support\ReferredProduct;

/**
 * 
 */
class MessageNotificationFactory
{
    /**
     * 
     */
    public function buildFromPayload(array $metadata, array $message, array $contact): MessageNotification
    {
        $notification = $this->buildMessageNotification($metadata, $message);

        return $this->decorateNotification($notification, $message, $contact);
    }

    /**
     * 
     */
    private function buildMessageNotification(array $metadata, array $message): MessageNotification
    {
        switch ($message['type']) {
            case 'text':
                return new Text(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['text']['body'],
                    $message['timestamp']
                );
            case 'reaction':
                return new Reaction(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['reaction']['message_id'],
                    $message['reaction']['emoji'],
                    $message['timestamp']
                );
            case 'sticker':
            case 'image':
            case 'document':
            case 'audio':
            case 'video':
            case 'voice':
                return new Media(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message[$message['type']]['id'],
                    $message[$message['type']]['mime_type'],
                    $message[$message['type']]['caption'] ?? '',
                    $message['timestamp']
                );
            case 'location':
                return new Location(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['location']['latitude'],
                    $message['location']['longitude'],
                    $message['location']['name'] ?? '',
                    $message['location']['address'] ?? '',
                    $message['timestamp']
                );
            case 'contacts':
                return new Contact(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['contacts'][0]['addresses'] ?? [],
                    $message['contacts'][0]['emails'] ?? [],
                    $message['contacts'][0]['name'],
                    $message['contacts'][0]['org'] ?? [],
                    $message['contacts'][0]['phones'],
                    $message['contacts'][0]['urls'] ?? [],
                    $message['contacts'][0]['birthday'] ?? null,
                    $message['timestamp']
                );
            case 'button':
                return new Button(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['button']['text'],
                    $message['button']['payload'],
                    $message['timestamp']
                );
            case 'interactive':
                return new Interactive(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['interactive']['list_reply']['id'] ?? $message['interactive']['button_reply']['id'],
                    $message['interactive']['list_reply']['title'] ?? $message['interactive']['button_reply']['title'],
                    $message['interactive']['list_reply']['description'] ?? '',
                    $message['timestamp']
                );
            case 'order':
                return new Order(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['order']['catalog_id'],
                    $message['order']['text'] ?? '',
                    new Support\Products($message['order']['product_items']),
                    $message['timestamp']
                );
            case 'system':
                return new System(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    new Business($message['system']['customer'], ''),
                    $message['system']['body'],
                    $message['timestamp']
                );
            case 'unknown':
            default:
                return new Unknown(
                    $message['id'],
                    new Business($metadata['phone_number_id'], $metadata['display_phone_number']),
                    $message['timestamp']
                );
        }
    }

    /**
     * 
     */
    private function decorateNotification(MessageNotification $notification, array $message, array $contact): MessageNotification
    {
        if ($contact) {
            $notification->withCustomer(new Customer(
                $contact['wa_id'],
                $contact['profile']['name'],
                $message['from']
            ));
        }

        if (isset($message['context'])) {
            if (isset($message['context']['referred_product'])) {
                $referred_product = new ReferredProduct(
                    $message['context']['referred_product']['catalog_id'],
                    $message['context']['referred_product']['product_retailer_id']
                );
            }

            $notification->withContext(new Context(
                $message['context']['id'] ?? null,
                $message['context']['forwarded'] ?? false,
                $referred_product ?? null
            ));
        }

        if (isset($message['referral'])) {
            $notification->withReferral(new Referral(
                $message['referral']['source_id'] ?? '',
                $message['referral']['source_url'] ?? '',
                $message['referral']['source_type'] ?? '',
                $message['referral']['headline'] ?? '',
                $message['referral']['body'] ?? '',
                $message['referral']['media_type'] ?? '',
                $message['referral']['image_url'] ?? $message['referral']['video_url'] ?? '',
                $message['referral']['thumbnail_url'] ?? ''
            ));
        }

        return $notification;
    }
}
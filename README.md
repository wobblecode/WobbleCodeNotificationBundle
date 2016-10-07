
# WobbleCodeNotificationBundle

[WIP] Notification based on events

## Channels

Channels are defined per project in your main services config Eg:

```yaml
services:
    wobblecode_notification.channel.ui:
        class: WobbleCode\NotificationBundle\Channel\UiChannel
        arguments:
            - "@wobblecode_notification.notification_manager"
            - "@twig"
    wobblecode_notification.channel.mail:
        class: WobbleCode\NotificationBundle\Channel\MailChannel
        arguments:
            - "@instasent_resque.resque"
            - "@twig"
            - "@swiftmailer.mailer.default"
            - "@swiftmailer.mailer.default.transport.real"
```

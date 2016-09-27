<?php

namespace WobbleCode\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\DateTimePickerType;
use WobbleCode\UIKitGeckoBundle\Form\Type\EntitySelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\JsonType;
use WobbleCode\UIKitGeckoBundle\Form\Type\SwitchType;

class SubscriptionUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', SwitchType::class, array(
                'label'       => 'subscription.enabled.label',
                'help_block'  => 'subscription.enabled.help',
                'attr' => array(
                    'placeholder' => 'subscription.enabled.placeholder',
                ),
                'horizontal'  => false,
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'text' => array(
                    'on' => 'subscription.enabled.text.on',
                    'off' => 'subscription.enabled.text.off'
                )
            ))

            ->add('visible', SwitchType::class, array(
                'label'       => 'subscription.visible.label',
                'help_block'  => 'subscription.visible.help',
                'attr' => array(
                    'placeholder' => 'subscription.visible.placeholder',
                ),
                'horizontal'  => false,
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'text' => array(
                    'on' => 'subscription.visible.text.on',
                    'off' => 'subscription.visible.text.off'
                )
            ))

            ->add('channels', JsonType::class, array(
                'label' => 'channel.params.label',
                'help_block' => 'channel.params.help',
                'attr' => [
                    'placeholder' => 'channel.params.placeholder',
                    'rows' => '5'
                ],
                'horizontal' => false
            ))

            ->add('createdAt', DateTimePickerType::class, array(
                'label'       => 'subscription.createdAt.label',
                'help_block'  => 'subscription.createdAt.help',
                'attr' => array(
                    'placeholder' => 'subscription.createdAt.placeholder',
                ),
                'horizontal'  => false,
                'addon_type'  => 'append',
                'addon_class' => 'addon-icon compact'
            ))
            ->add('updatedAt', DateTimePickerType::class, array(
                'label'       => 'subscription.updatedAt.label',
                'help_block'  => 'subscription.updatedAt.help',
                'attr' => array(
                    'placeholder' => 'subscription.updatedAt.placeholder',
                ),
                'horizontal'  => false,
                'addon_type'  => 'append',
                'addon_class' => 'addon-icon compact'
            ))
            ->add('event', EntitySelectType::class, array(
                'label'       => 'subscription.event.label',
                'help_block'  => 'subscription.event.help',
                'attr' => array(
                    'placeholder' => 'subscription.event.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\NotificationBundle\Document\Event'
            ))
            ->add('user', EntitySelectType::class, array(
                'label'       => 'subscription.user.label',
                'help_block'  => 'subscription.user.help',
                'attr' => array(
                    'placeholder' => 'subscription.user.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\UserBundle\Document\User'
            ))
            ->add('organization', EntitySelectType::class, array(
                'label'       => 'subscription.organization.label',
                'help_block'  => 'subscription.organization.help',
                'attr' => array(
                    'placeholder' => 'subscription.organization.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\UserBundle\Document\Organization'
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'subscription.save.label'
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'render_fieldset'    => false,
            'label_render'       => false,
            'show_legend'        => false,
            'translation_domain' => 'wc_notification',
            'data_class'         => 'WobbleCode\NotificationBundle\Document\Subscription'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wc_notification_subscription_user';
    }
}

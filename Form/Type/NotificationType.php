<?php

namespace WobbleCode\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\BootstrapSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;
use WobbleCode\UIKitGeckoBundle\Form\Type\MarkdownType;
use WobbleCode\UIKitGeckoBundle\Form\Type\SwitchType;

class NotificationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'label'       => 'notification.title.label',
                'help_block'  => 'notification.title.help',
                'attr' => array(
                    'placeholder' => 'notification.title.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('description', MarkdownType::class, array(
                'label'       => 'notification.description.label',
                'help_block'  => 'notification.description.help',
                'attr' => array(
                    'placeholder' => 'notification.description.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('status', BootstrapSelectType::class, array(
                'label'       => 'notification.status.label',
                'help_block'  => 'notification.status.help',
                'attr' => array(
                    'placeholder' => 'notification.status.placeholder',
                ),
                'empty_value' => 'Choose an option',
                'choices' => array(
                    'unread'    => 'Unread',
                    'read' => 'Read',
                ),
                'horizontal' => false
            ))
            ->add('level', BootstrapSelectType::class, array(
                'label'       => 'notification.level.label',
                'help_block'  => 'notification.level.help',
                'attr' => array(
                    'placeholder' => 'notification.level.placeholder',
                ),
                'empty_value' => 'Choose an option',
                'choices' => array(
                    100 => 'DEBUG',
                    200 => 'INFO',
                    250 => 'NOTICE',
                    280 => 'SUCCESS',
                    300 => 'WARNING',
                    400 => 'ERROR',
                    500 => 'CRITICAL',
                    550 => 'ALERT',
                    600 => 'EMERGENCY',
                ),
                'horizontal' => false
            ))
            ->add('interfacePlacement', null, array(
                'label'       => 'notification.interfacePlacement.label',
                'help_block'  => 'notification.interfacePlacement.help',
                'required'    => false,
                'attr' => array(
                    'placeholder' => 'notification.interfacePlacement.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('actionRequired', SwitchType::class, array(
                'label'       => 'notification.actionRequired.label',
                'help_block'  => 'notification.actionRequired.help',
                'attr' => array(
                    'placeholder' => 'notification.actionRequired.placeholder',
                ),
                'horizontal'  => false,
                'state' => array(
                    'on' => 'On',
                    'off' => ''
                ),
                'text' => array(
                    'on' => 'on',
                    'off' => 'off'
                )
            ))
            ->add('actionStatus', BootstrapSelectType::class, array(
                'label'       => 'notification.actionStatus.label',
                'help_block'  => 'notification.actionStatus.help',
                'attr' => array(
                    'placeholder' => 'notification.actionStatus.placeholder',
                ),
                'empty_value' => 'Choose an option',
                'choices' => array(
                    'pending'  => 'Pending',
                    'completed' => 'Completed'
                ),
                'horizontal' => false
            ))
            ->add('event', DocumentComboBoxType::class, array(
                'label'       => 'notification.event.label',
                'help_block'  => 'notification.event.help',
                'attr' => array(
                    'placeholder' => 'notification.event.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\NotificationBundle\Document\Event'
            ))
            ->add('user', DocumentComboBoxType::class, array(
                'label'       => 'notification.user.label',
                'help_block'  => 'notification.user.help',
                'attr' => array(
                    'placeholder' => 'notification.user.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\UserBundle\Document\User'
            ))
            ->add(
                'save',
                SubmitType::class,
                array(
                    'label' => 'notification.save.label'
                )
            )
        ;
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
            'data_class'         => 'WobbleCode\NotificationBundle\Document\Notification'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wc_notification_notification';
    }
}

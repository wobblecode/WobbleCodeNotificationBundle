<?php

namespace WobbleCode\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use WobbleCode\UIKitGeckoBundle\Form\Type\BootstrapSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\MarkdownType;
use WobbleCode\UIKitGeckoBundle\Form\Type\SwitchType;

class NotifyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('event', DocumentSelectType::class, array(
                'label'       => 'notify.event.label',
                'help_block'  => 'notify.event.help',
                'attr' => array(
                    'placeholder' => 'notify.event.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCodeNotificationBundle:Event',
                'query_builder' => function ($qb) {
                    return $qb->createQueryBuilder('WobbleCodeNotificationBundle:Event')
                              ->field('manual')->equals(true);
                }
            ))
            ->add('organizations', DocumentComboBoxType::class, array(
                'label'       => 'notify.organizations.label',
                'help_block'  => 'notify.organizations.help',
                'multiple'    => true,
                'attr' => array(
                    'placeholder' => 'notify.organizations.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\UserBundle\Document\Organization'
            ))
            ->add('users', DocumentComboBoxType::class, array(
                'label'       => 'notify.users.label',
                'help_block'  => 'notify.users.help',
                'multiple'    => true,
                'attr' => array(
                    'placeholder' => 'notify.users.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\UserBundle\Document\User'
            ))
            ->add('language', BootstrapSelectType::class, array(
                'label'       => 'notify.language.label',
                'help_block'  => 'notify.language.help',
                'attr' => array(
                    'placeholder' => 'notify.language.placeholder',
                ),
                'empty_value' => 'All Languages',
                'choices' => array(
                    'es' => 'Spanish',
                    'en' => 'English'
                ),
                'horizontal' => false
            ))
            ->add('level', BootstrapSelectType::class, array(
                'label'       => 'notify.level.label',
                'help_block'  => 'notify.level.help',
                'attr' => array(
                    'placeholder' => 'notify.level.placeholder',
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
            ->add('interfacePlacement', BootstrapSelectType::class, array(
                'label'       => 'notify.interfacePlacement.label',
                'help_block'  => 'notify.interfacePlacement.help',
                'attr' => array(
                    'placeholder' => 'notify.interfacePlacement.placeholder',
                ),
                'empty_value' => 'Only in notifications',
                'choices' => ["Notifications"],
                'horizontal' => false
            ))
            ->add('subject', null, array(
                'label'       => 'notify.subject.label',
                'help_block'  => 'notify.subject.help',
                'attr' => array(
                    'placeholder' => 'notify.subject.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('message', MarkdownType::class, array(
                'label'       => 'notify.message.label',
                'help_block'  => 'notify.message.help',
                'attr' => array(
                    'placeholder' => 'notify.message.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('actionRequired', SwitchType::class, array(
                'label'       => 'notify.actionRequired.label',
                'help_block'  => 'notify.actionRequired.help',
                'attr' => array(
                    'placeholder' => 'notify.actionRequired.placeholder',
                    'required' => false,
                ),
                'horizontal'  => false,
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'text' => array(
                    'on' => 'on',
                    'off' => 'off'
                )
            ))
            ->add('forceChannels', SwitchType::class, array(
                'label'       => 'notify.forceChannels.label',
                'help_block'  => 'notify.forceChannels.help',
                'attr' => array(
                    'placeholder' => 'notify.forceChannels.placeholder',
                ),
                'horizontal'  => false,
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'text' => array(
                    'on' => 'on',
                    'off' => 'off'
                )
            ))
            ->add(
                'send',
                SubmitType::class,
                array(
                    'label' => 'notify.send.label'
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
            'data_class'         => 'WobbleCode\NotificationBundle\Model\Notify'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wc_notification_notify';
    }
}

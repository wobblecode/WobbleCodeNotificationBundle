<?php

namespace WobbleCode\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\JsonType;
use WobbleCode\UIKitGeckoBundle\Form\Type\SwitchType;

class SubscriptionType extends AbstractType
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
                    'on' => 'on',
                    'off' => 'off'
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
                    'on' => 'on',
                    'off' => 'off'
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
            ->add('event', DocumentSelectType::class, array(
                'label'       => 'subscription.event.label',
                'help_block'  => 'subscription.event.help',
                'attr' => array(
                    'placeholder' => 'subscription.event.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\NotificationBundle\Document\Event'
            ))
            ->add('user', DocumentSelectType::class, array(
                'label'       => 'subscription.user.label',
                'help_block'  => 'subscription.user.help',
                'attr' => array(
                    'placeholder' => 'subscription.user.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\UserBundle\Document\User'
            ))
            ->add('organization', DocumentSelectType::class, array(
                'label'       => 'subscription.organization.label',
                'help_block'  => 'subscription.organization.help',
                'attr' => array(
                    'placeholder' => 'subscription.organization.placeholder',
                ),
                'horizontal'  => false,
                'class'       => 'WobbleCode\UserBundle\Document\Organization'
            ))
            ->add('expressionRule', TextareaType::class, array(
                'label'       => 'subscription.expressionRule.label',
                'help_block'  => 'subscription.expressionRule.help',
                'required'    => false,
                'attr' => array(
                    'rows' => 4,
                    'placeholder' => 'subscription.expressionRule.placeholder',
                ),
                'horizontal' => false
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
        return 'wc_notification_subscription';
    }
}

<?php

namespace WobbleCode\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use WobbleCode\UIKitGeckoBundle\Form\Type\BootstrapSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentComboBoxType;
use WobbleCode\UIKitGeckoBundle\Form\Type\DocumentSelectType;
use WobbleCode\UIKitGeckoBundle\Form\Type\JsonType;
use WobbleCode\UIKitGeckoBundle\Form\Type\SwitchType;
use WobbleCode\UIKitGeckoBundle\Form\Type\TagsType;

class ChannelType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                null,
                [
                    'label' => 'channel.name.label',
                    'help_block' => 'channel.name.help',
                    'attr' => [
                        'placeholder' => 'channel.name.placeholder',
                    ],
                    'horizontal' => false
                ]
            )
            ->add(
                'service',
                null,
                [
                    'label' => 'channel.service.label',
                    'help_block' => 'channel.service.help',
                    'attr' => [
                        'placeholder' => 'channel.service.placeholder',
                    ],
                    'horizontal' => false
                ]
            )
            ->add(
                'params',
                JsonType::class,
                [
                    'label' => 'channel.params.label',
                    'help_block' => 'channel.params.help',
                    'attr' => [
                        'placeholder' => 'channel.params.placeholder',
                        'rows' => '5'
                    ],
                    'horizontal' => false
                ]
            )
            ->add(
                'save',
                SubmitType::class
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'render_fieldset' => false,
            'label_render' => false,
            'show_legend' => false,
            'translation_domain' => 'wc_notification',
            'data_class' => 'WobbleCode\NotificationBundle\Document\Channel',
            'roles' => []
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wc_notification_channel';
    }
}

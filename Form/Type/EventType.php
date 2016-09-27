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

class EventType extends AbstractType
{

    protected $defaults;

    public function __construct($defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $defaultsArray = array_merge(
            array(
            'enabled' => 'event.defaults.choice.enabled',
            'visible' => 'event.defaults.choice.visible',
            ),
            $this->defaults
        );

        $builder
            ->add('key', null, array(
                'label'       => 'event.key.label',
                'help_block'  => 'event.key.help',
                'attr' => array(
                    'placeholder' => 'event.key.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('manual', SwitchType::class, array(
                'label'       => 'event.manual.label',
                'help_block'  => 'event.manual.help',
                'state' => array(
                    'on' => 'success',
                    'off' => ''
                ),
                'horizontal' => false
            ))
            ->add('description', TextareaType::class, array(
                'label'       => 'event.description.label',
                'help_block'  => 'event.description.help',
                'attr' => array(
                    'placeholder' => 'event.description.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('roles', ChoiceType::class, array(
                'expanded' => true,
                'multiple' => true,
                'label'       => 'event.roles.label',
                'help_block'  => 'event.roles.help',
                'attr' => array(
                    'placeholder' => 'event.roles.placeholder',
                ),
                'choices' => $this->flattenRoles($options['roles']),
                'horizontal' => false
            ))
            ->add('defaults', ChoiceType::class, array(
                'expanded' => true,
                'multiple' => true,
                'label'       => 'event.defaults.label',
                'help_block'  => 'event.defaults.help',
                'attr' => array(
                    'placeholder' => 'event.defaults.placeholder',
                ),
                'choices' => $defaultsArray,
                'horizontal' => false
            ))
            ->add('channels', null, array(
                'multiple'    => true,
                'expanded'    => true,
                'label'       => 'event.channels.label',
                'help_block'  => 'event.channels.help',
                'class'       => 'WobbleCode\NotificationBundle\Document\Channel',
                'attr' => array(
                    'placeholder' => 'event.channels.placeholder',
                ),
                'horizontal' => false
            ))
            ->add('level', BootstrapSelectType::class, array(
                'label'       => 'event.level.label',
                'help_block'  => 'event.level.help',
                'attr' => array(
                    'placeholder' => 'event.level.placeholder',
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
            ->add(
                'tags',
                TagsType::class,
                array(
                    'horizontal_input_wrapper_class' => '',
                    'required' => false,
                    'pluginOptions' => array(
                        'tags' => [],
                        'containerCssClass' => 'form-control'
                    )
                )
            )
            ->add('expressionRule', TextareaType::class, array(
                'label'       => 'event.expressionRule.label',
                'help_block'  => 'event.expressionRule.help',
                'required' => false,
                'attr' => array(
                    'rows' => 4,
                    'placeholder' => 'event.expressionRule.placeholder',
                ),
                'horizontal' => false,
            ))
            ->add('uiTemplate', TextareaType::class, array(
                'label'       => 'event.uiTemplate.label',
                'help_block'  => 'event.uiTemplate.help',
                'attr' => array(
                    'placeholder' => 'event.uiTemplate.placeholder',
                ),
                'horizontal' => false,
            ))
            ->add(
                'channelTemplates',
                JsonType::class,
                [
                    'label' => 'event.channelTemplate.label',
                    'help_block' => 'event.channelTemplate.help',
                    'attr' => [
                        'placeholder' => 'event.channelTemplate.placeholder',
                        'rows' => '5'
                    ],
                    'horizontal' => false
                ]
            )
            ->add('fromEmail', null, array(
                'label'       => 'event.fromEmail.label',
                'help_block'  => 'event.fromEmail.help',
                'attr' => array(
                    'placeholder' => 'event.fromEmail.placeholder',
                ),
                'horizontal'  => false,
            ))
            ->add('fromName', null, array(
                'label'       => 'event.fromName.label',
                'help_block'  => 'event.fromName.help',
                'attr' => array(
                    'placeholder' => 'event.fromName.placeholder',
                ),
                'horizontal'  => false,
                'required' => false,
            ))
            ->add(
                'bcc',
                TagsType::class,
                array(
                    'label'       => 'event.bcc.label',
                    'help_block'  => 'event.bcc.help',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'event.bcc.placeholder',
                    ),
                    'horizontal_input_wrapper_class' => '',
                    'pluginOptions' => array(
                        'tags' => [],
                        'containerCssClass' => 'form-control'
                    )
                )
            )
            ->add(
                'update',
                SwitchType::class,
                array(
                    'label'       => 'event.update.label',
                    'help_block'  => 'event.update.help',
                    'attr' => array(
                        'placeholder' => 'event.update.placeholder',
                    ),
                    'state' => array(
                        'on' => 'success',
                        'off' => ''
                    ),
                    'horizontal' => false,
                    'mapped' => false,
                )
            )
            ->add('save', SubmitType::class, array(
                'label' => 'event.save.label',
            ))
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
            'data_class'         => 'WobbleCode\NotificationBundle\Document\Event',
            'roles'              => []
        ));
    }

    private function flattenRoles($roleHierarchy)
    {
        $flattenRoles = [];

        foreach ($roleHierarchy as $roles) {
            foreach ($roles as $role) {
                $flattenRoles[$role] = $role;
            }
        }

        return $flattenRoles;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'wc_notification_event';
    }
}

<?php

namespace Mautic\ChannelBundle\Form\Type;

use Mautic\ChannelBundle\Model\MessageModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessageSendType extends AbstractType
{
    protected \Symfony\Component\Routing\RouterInterface $router;
    protected \Mautic\ChannelBundle\Model\MessageModel $messageModel;

    public function __construct(RouterInterface $router, MessageModel $messageModel)
    {
        $this->router       = $router;
        $this->messageModel = $messageModel;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'marketingMessage',
            MessageListType::class,
            [
                'label'       => 'mautic.channel.send.selectmessages',
                'label_attr'  => ['class' => 'control-label'],
                'multiple'    => false,
                'required'    => true,
                'constraints' => [
                    new NotBlank(
                        ['message' => 'mautic.channel.choosemessage.notblank']
                    ),
                ],
            ]
        );

        if (!empty($options['update_select'])) {
            $windowUrl = $this->router->generate(
                'mautic_message_action',
                [
                    'objectAction' => 'new',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'newMarketingMessageButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'   => 'btn btn-primary btn-nospin',
                        'onclick' => 'Mautic.loadNewWindow({windowUrl: \''.$windowUrl.'\'})',
                        'icon'    => 'fa fa-plus',
                    ],
                    'label' => 'mautic.channel.create.new.message',
                ]
            );

            // create button edit email
            $windowUrlEdit = $this->router->generate(
                'mautic_message_action',
                [
                    'objectAction' => 'edit',
                    'objectId'     => 'messageId',
                    'contentOnly'  => 1,
                    'updateSelect' => $options['update_select'],
                ]
            );

            $builder->add(
                'editMessageButton',
                ButtonType::class,
                [
                    'attr' => [
                        'class'    => 'btn btn-primary btn-nospin',
                        'onclick'  => 'Mautic.loadNewWindow({windowUrl: \''.$windowUrlEdit.'\'})',
                        'disabled' => !isset($options['data']['message']),
                        'icon'     => 'fa fa-edit',
                    ],
                    'label' => 'mautic.channel.send.edit.message',
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['update_select']);
    }
}

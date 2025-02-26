<?php

namespace Mautic\CoreBundle\Form\Type;

use Mautic\CoreBundle\Helper\ThemeHelperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeListType extends AbstractType
{
    private \Mautic\CoreBundle\Helper\ThemeHelperInterface $themeHelper;

    public function __construct(ThemeHelperInterface $helper)
    {
        $this->themeHelper = $helper;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'choices'           => function (Options $options) {
                    $themes                     = $this->themeHelper->getInstalledThemes($options['feature']);
                    $themes['mautic_code_mode'] = 'Code Mode';

                    return array_flip($themes);
                },
                'expanded'          => false,
                'multiple'          => false,
                'label'             => 'mautic.core.form.theme',
                'label_attr'        => ['class' => 'control-label'],
                'placeholder'       => false,
                'required'          => false,
                'attr'              => [
                    'class' => 'form-control',
                ],
                'feature'           => 'all',
            ]
        );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

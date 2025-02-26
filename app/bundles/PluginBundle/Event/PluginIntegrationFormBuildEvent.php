<?php

namespace Mautic\PluginBundle\Event;

use Mautic\PluginBundle\Integration\UnifiedIntegrationInterface;
use Symfony\Component\Form\FormBuilderInterface;

class PluginIntegrationFormBuildEvent extends AbstractPluginIntegrationEvent
{
    private array $options;

    private \Symfony\Component\Form\FormBuilderInterface $builder;

    public function __construct(UnifiedIntegrationInterface $integration, FormBuilderInterface $builder, array $options)
    {
        $this->integration = $integration;
        $this->builder     = $builder;
        $this->options     = $options;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getFormBuilder()
    {
        return $this->builder;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}

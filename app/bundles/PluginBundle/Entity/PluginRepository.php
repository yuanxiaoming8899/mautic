<?php

namespace Mautic\PluginBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<Plugin>
 */
class PluginRepository extends CommonRepository
{
    /**
     * Find an addon record by bundle name.
     *
     * @param string $bundle
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByBundle($bundle)
    {
        $q = $this->createQueryBuilder($this->getTableAlias());
        $q->where($q->expr()->eq('p.bundle', ':bundle'))
            ->setParameter('bundle', $bundle);

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(array $args = [])
    {
        $q = $this->_em->createQueryBuilder();
        $q->select($this->getTableAlias())
            ->from(\Mautic\PluginBundle\Entity\Plugin::class, $this->getTableAlias(), (!empty($args['index'])) ? $this->getTableAlias().'.'.$args['index'] : $this->getTableAlias().'.id');

        $args['qb']               = $q;
        $args['ignore_paginator'] = true;

        return parent::getEntities($args);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOrder(): array
    {
        return [
            ['p.name', 'ASC'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return 'p';
    }
}

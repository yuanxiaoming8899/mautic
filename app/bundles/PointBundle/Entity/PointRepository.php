<?php

namespace Mautic\PointBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<Point>
 */
class PointRepository extends CommonRepository
{
    /**
     * {@inheritdoc}
     */
    public function getEntities(array $args = [])
    {
        $q = $this->_em
            ->createQueryBuilder()
            ->select($this->getTableAlias().', cat')
            ->from(\Mautic\PointBundle\Entity\Point::class, $this->getTableAlias())
            ->leftJoin($this->getTableAlias().'.category', 'cat')
            ->leftJoin($this->getTableAlias().'.group', 'pl');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return 'p';
    }

    /**
     * Get array of published actions based on type.
     *
     * @param string $type
     *
     * @return array
     */
    public function getPublishedByType($type)
    {
        $q = $this->createQueryBuilder('p')
            ->select('partial p.{id, type, name, delta, repeatable, properties}')
            ->setParameter('type', $type);

        // make sure the published up and down dates are good
        $expr = $this->getPublishedByDateExpression($q);
        $expr->add($q->expr()->eq('p.type', ':type'));

        $q->where($expr);

        return $q->getQuery()->getResult();
    }

    /**
     * @param string $type
     * @param int    $leadId
     */
    public function getCompletedLeadActions($type, $leadId): array
    {
        $q = $this->_em->getConnection()->createQueryBuilder()
            ->select('p.*')
            ->from(MAUTIC_TABLE_PREFIX.'point_lead_action_log', 'x')
            ->innerJoin('x', MAUTIC_TABLE_PREFIX.'points', 'p', 'x.point_id = p.id');

        // make sure the published up and down dates are good
        $q->where(
            $q->expr()->and(
                $q->expr()->eq('p.type', ':type'),
                $q->expr()->eq('x.lead_id', (int) $leadId)
            )
        )
            ->setParameter('type', $type);

        $results = $q->executeQuery()->fetchAllAssociative();

        $return = [];

        foreach ($results as $r) {
            $return[$r['id']] = $r;
        }

        return $return;
    }

    /**
     * @param int $leadId
     */
    public function getCompletedLeadActionsByLeadId($leadId): array
    {
        $q = $this->_em->getConnection()->createQueryBuilder()
            ->select('p.*')
            ->from(MAUTIC_TABLE_PREFIX.'point_lead_action_log', 'x')
            ->innerJoin('x', MAUTIC_TABLE_PREFIX.'points', 'p', 'x.point_id = p.id');

        // make sure the published up and down dates are good
        $q->where(
            $q->expr()->and(
                $q->expr()->eq('x.lead_id', (int) $leadId)
            )
        );

        $results = $q->executeQuery()->fetchAllAssociative();

        $return = [];

        foreach ($results as $r) {
            $return[$r['id']] = $r;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    protected function addCatchAllWhereClause($q, $filter)
    {
        return $this->addStandardCatchAllWhereClause($q, $filter, [
            'p.name',
            'p.description',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function addSearchCommandWhereClause($q, $filter)
    {
        return $this->addStandardSearchCommandWhereClause($q, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCommands()
    {
        return $this->getStandardSearchCommands();
    }
}

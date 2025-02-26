<?php

namespace Mautic\FormBundle\Model;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Model\MauticModelInterface;
use Mautic\FormBundle\Entity\Submission;
use Mautic\FormBundle\Entity\SubmissionRepository;

class SubmissionResultLoader implements MauticModelInterface
{
    private \Doctrine\ORM\EntityManager $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return Submission|null
     */
    public function getSubmissionWithResult($id)
    {
        $repository = $this->getRepository();

        return $repository->getEntity($id);
    }

    /**
     * @return SubmissionRepository
     */
    private function getRepository()
    {
        return $this->entityManager->getRepository(Submission::class);
    }
}

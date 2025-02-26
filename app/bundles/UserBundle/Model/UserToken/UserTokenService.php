<?php

namespace Mautic\UserBundle\Model\UserToken;

use Mautic\CoreBundle\Helper\RandomHelper\RandomHelperInterface;
use Mautic\UserBundle\Entity\UserToken;
use Mautic\UserBundle\Entity\UserTokenRepositoryInterface;

final class UserTokenService implements UserTokenServiceInterface
{
    private \Mautic\CoreBundle\Helper\RandomHelper\RandomHelperInterface $randomHelper;

    private \Mautic\UserBundle\Entity\UserTokenRepositoryInterface $userTokenRepository;

    public function __construct(
        RandomHelperInterface $randomHelper,
        UserTokenRepositoryInterface $userTokenRepository
    ) {
        $this->randomHelper        = $randomHelper;
        $this->userTokenRepository = $userTokenRepository;
    }

    /**
     * @param int $secretLength
     *
     * @return UserToken
     */
    public function generateSecret(UserToken $token, $secretLength = 32)
    {
        do {
            $randomSecret   = $this->randomHelper->generate($secretLength);
            $isSecretUnique = $this->userTokenRepository->isSecretUnique($randomSecret);
        } while (false === $isSecretUnique);

        return $token->setSecret($randomSecret);
    }

    /**
     * @return bool
     */
    public function verify(UserToken $token)
    {
        return $this->userTokenRepository->verify($token);
    }
}

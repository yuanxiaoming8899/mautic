<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Auth\Support\Oauth2\Token;

use kamermans\OAuth2\Token\TokenInterface;
use kamermans\OAuth2\Token\TokenSerializer;

class IntegrationToken implements TokenInterface
{
    // Pull in serialize() and unserialize() methods
    use TokenSerializer;

    /**
     * @var mixed[]
     */
    private array $extraData;

    /**
     * @param mixed[] $extraData
     */
    public function __construct(?string $accessToken, ?string $refreshToken, $expiresAt = null, array $extraData = [])
    {
        $this->accessToken  = (string) $accessToken;
        $this->refreshToken = (string) $refreshToken;
        $this->expiresAt    = (int) $expiresAt;
        $this->extraData    = $extraData;
    }

    /**
     * @return string The access token
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string The refresh token
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return int The expiration timestamp
     */
    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt && $this->expiresAt < time();
    }

    public function getExtraData(): array
    {
        return $this->extraData;
    }
}

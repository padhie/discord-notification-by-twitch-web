<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('csrf_token');

        if (!is_string($username)) {
            throw new AuthenticationException('Username or password are invalid.');
        }

        if (!is_string($password)) {
            throw new AuthenticationException('Username or password are invalid.');
        }

        if (!is_string($csrfToken) && $csrfToken !== null) {
            throw new AuthenticationException('Username or password are invalid.');
        }

        $user = $this->userRepository->findOneByUsername($username);
        if ($user === null) {
            throw new AuthenticationException('Username or password are invalid.');
        }

        return new Passport(
            new UserBadge($username, function (?string $username): ?User {
                return $this->userRepository->findOneByUsername($username);
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new PasswordUpgradeBadge($password, $this->userRepository),
            ]
        );
    }

    public function supports(Request $request): bool
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('login');
    }
}

<?php

declare(strict_types=1);

namespace Terminal42\AutoRegistrationBundle\EventListener;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\ModuleRegistration;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class RegistrationListener
{
    /**
     * @param UserProviderInterface<FrontendUser> $userProvider
     */
    public function __construct(
        private readonly UserProviderInterface $userProvider,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly Connection $connection,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RequestStack $requestStack,
        private readonly UserCheckerInterface $userChecker,
        private readonly AuthenticationSuccessHandlerInterface $authenticationSuccessHandler,
    ) {}

    /**
     * Within the registration process, log in the user if needed.
     *
     * @param array<string, mixed> $data
     */
    #[AsHook('createNewUser')]
    public function onCreateNewUser(int $userId, array &$data, ModuleRegistration $module): void
    {
        if ('activate' !== $module->reg_autoActivate && 'login' !== $module->reg_autoActivate) {
            return;
        }

        $disableValue = version_compare(ContaoCoreBundle::getVersion(), '5', '<') ? '' : 0;

        $affectedRows = $this->connection->update('tl_member', ['disable' => $disableValue], ['id' => $userId]);

        if ('login' === $module->reg_autoActivate && $affectedRows > 0) {
            $this->loginUser($data['username']);
        }
    }

    /**
     * Within the activation process, log in the user if needed.
     */
    #[AsHook('activateAccount')]
    public function onActivateAccount(MemberModel $member, ModuleRegistration $module): void
    {
        if ($module->reg_activateLogin) {
            $this->loginUser($member->username);
        }
    }

    /**
     * Actually log in the user by given username.
     */
    private function loginUser(string $username): void
    {
        try {
            $user = $this->userProvider->loadUserByIdentifier($username);
        } catch (UserNotFoundException) {
            return;
        }

        if (!$user instanceof FrontendUser) {
            return;
        }

        try {
            $this->userChecker->checkPreAuth($user);
            $this->userChecker->checkPostAuth($user);
        } catch (AccountStatusException) {
            return;
        }

        $usernamePasswordToken = new UsernamePasswordToken($user, 'frontend', $user->getRoles());
        $this->tokenStorage->setToken($usernamePasswordToken);

        $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $usernamePasswordToken);
        $this->eventDispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);

        $this->logger->log(
            LogLevel::INFO,
            'User "' . $username . '" was logged in automatically',
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::ACCESS)],
        );

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $request->request->set('_target_path', base64_encode($request->getRequestUri()));

        $this->authenticationSuccessHandler->onAuthenticationSuccess($request, $usernamePasswordToken);
    }
}

<?php

declare(strict_types=1);

namespace Terminal42\AutoRegistrationBundle\EventListener;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\CoreBundle\ServiceAnnotation\Hook;
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
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class RegistrationListener
{
    private UserProviderInterface $userProvider;
    private TokenStorageInterface $tokenStorage;
    private Connection $connection;
    private LoggerInterface $logger;
    private EventDispatcherInterface $eventDispatcher;
    private RequestStack $requestStack;
    private UserCheckerInterface $userChecker;
    private AuthenticationSuccessHandlerInterface $authenticationSuccessHandler;

    /**
     * RegistrationListener constructor.
     */
    public function __construct(UserProviderInterface $userProvider, TokenStorageInterface $tokenStorage, Connection $connection, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, UserCheckerInterface $userChecker, AuthenticationSuccessHandlerInterface $authenticationSuccessHandler)
    {
        $this->userProvider = $userProvider;
        $this->tokenStorage = $tokenStorage;
        $this->connection = $connection;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->userChecker = $userChecker;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    /**
     * Within the registration process, log in the user if needed.
     *
     * @Hook("createNewUser")
     */
    public function onCreateNewUser(int $userId, array &$data, ModuleRegistration $module): void
    {
        if ('activate' !== $module->reg_autoActivate && 'login' !== $module->reg_autoActivate) {
            return;
        }

        $data['disable'] = '';
        $match = $this->connection->createQueryBuilder()
            ->update('tl_member')
            ->set('disable', ':disable')
            ->where('id=:id')
            ->setParameter('id', $userId)
            ->setParameter('disable', '')
            ->execute()
        ;

        if ('login' === $module->reg_autoActivate && $match) {
            $this->loginUser($data['username']);
        }
    }

    /**
     * Within the activation process, log in the user if needed.
     *
     * @Hook("activateAccount")
     */
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
            $user = $this->userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $exception) {
            return;
        }

        if (!$user instanceof FrontendUser) {
            return;
        }

        try {
            $this->userChecker->checkPreAuth($user);
            $this->userChecker->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            return;
        }

        $usernamePasswordToken = new UsernamePasswordToken($user, null, 'frontend', $user->getRoles());
        $this->tokenStorage->setToken($usernamePasswordToken);

        $event = new InteractiveLoginEvent($this->requestStack->getCurrentRequest(), $usernamePasswordToken);
        $this->eventDispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);

        $this->logger->log(
            LogLevel::INFO,
            'User "'.$username.'" was logged in automatically',
            ['contao' => new ContaoContext(__METHOD__, TL_ACCESS)]
        );

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $request->request->set('_target_path', base64_encode($request->getRequestUri()));

        $this->authenticationSuccessHandler->onAuthenticationSuccess($request, $usernamePasswordToken);
    }
}

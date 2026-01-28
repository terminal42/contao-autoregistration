<?php

declare(strict_types=1);

namespace Terminal42\AutoRegistrationBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\ModuleRegistration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RegistrationListener
{
    /**
     * @param UserProviderInterface<FrontendUser> $userProvider
     */
    public function __construct(
        private readonly Security $security,
        private readonly UserProviderInterface $userProvider,
        private readonly Connection $connection,
        private readonly RequestStack $requestStack,
        private readonly string $authenticatorName = 'contao.security.login_authenticator.contao_frontend',
    ) {
    }

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

        $data['disable'] = false;
        $affectedRows = $this->connection->update('tl_member', ['disable' => false], ['id' => $userId], ['disable' => Types::BOOLEAN]);

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

        // The target path does not matter, because we do not use the login response but let the registration module
        // generate the response.
        $this->requestStack->getCurrentRequest()->request->set('_target_path', base64_encode('/'));

        $this->security->login($user, $this->authenticatorName);
    }
}

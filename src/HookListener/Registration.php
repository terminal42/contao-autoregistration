<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 04.06.18
 * Time: 11:47
 */

namespace Terminal42\ContaoAutoRegistrationBundle\HookListener;


use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Registration
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CreateNewUserListener constructor.
     *
     * @param UserProviderInterface $userProvider The user provider.
     * @param TokenStorageInterface $tokenStorage The token storage.
     * @param Session               $session      The session.
     * @param Connection            $connection   The database connection.
     * @param LoggerInterface       $logger
     */
    public function __construct(
        UserProviderInterface $userProvider,
        TokenStorageInterface $tokenStorage,
        Session $session,
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->userProvider = $userProvider;
        $this->tokenStorage = $tokenStorage;
        $this->session      = $session;
        $this->connection   = $connection;
        $this->logger       = $logger;
    }

    public function processRegistration($userId, $arrData)
    {
        global $objPage;

        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('tl_page')
            ->where('id=:id')
            ->setParameter('id', $objPage->rootId)
            ->execute();

        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            return;
        }

        if ($result->auto_activate_registration) {
            $match = $this->connection->createQueryBuilder()
                ->update('tl_member')
                ->set('disable', '')
                ->where('id=:id')
                ->execute($userId);

            // TODO support where

            if ($result->auto_login_registration && $match) {
                $this->import('FrontendUser', 'User');
                // TODO rework this one
                $this->User->login();
            }
        }
    }

    public function activateAccount($member)
    {
        global $objPage;

        $statement =
            $this->connection->createQueryBuilder()
                ->select('*')
                ->from('tl_page')
                ->where('id=:id')
                ->setParameter('id', $objPage->rootId)
                ->execute();

        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            return;
        }

        if ($result->auto_login_activation) {
            // Authenticate user
            $user = $this->userProvider->loadUserByUsername($member->username);

            $usernamePasswordToken = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->tokenStorage->setToken($usernamePasswordToken);
            $this->session->set('_security_main', serialize($usernamePasswordToken));

            $this->logger->log(
                LogLevel::INFO,
                'User "' . $member->username . '" was logged in automatically',
                array('contao' => new ContaoContext(__METHOD__, TL_ACCESS))
            );
        }
    }
}

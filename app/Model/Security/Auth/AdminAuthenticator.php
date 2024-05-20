<?php

namespace App\Model\Security\Auth;

use App\Model\Database\DatabaseManager;
use App\Model\Database\Entity\System\AccessLog;
use App\Model\Database\Entity\System\User;
use App\Model\Database\EntityRepository;
use App\Model\Database\Exceptions\EntityNotFoundException;
use App\Model\Security\Auth\Exceptions\BadCredentialsException;
use App\Model\Security\Auth\Exceptions\LoggedOutException;
use Nette\Database\Table\ActiveRow;
use Nette\Http\Session;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;

/**
 * Class AdminAuthenticator
 * @package App\Model\Security\SessionAuth
 */
class AdminAuthenticator
{
    const SESSION_SECTION = 'admin_login';
    const EXPIRATION = "14 days";

    private Passwords $passwords;
    private Session $session;

    private EntityRepository $adminRepository;

    /**
     * Authenticator constructor.
     * @param Passwords $passwords
     * @param Session $session
     * @param DatabaseManager $databaseManager
     */
    public function __construct(Passwords $passwords, Session $session, private DatabaseManager $databaseManager)
    {
        $this->passwords = $passwords;
        $this->session = $session;
        $this->adminRepository = $this->databaseManager->getEntityRepository(User::class);
    }

    /**
     * @param array $credentials
     * @param string $expiration
     * @throws BadCredentialsException
     */
    public function login(array $credentials, string $expiration = "14 days"): void
    {
        [$email, $password] = $credentials;
        $logRepository = $this->databaseManager->getEntityRepository(AccessLog::class);

        $admin = $this->adminRepository->findByColumn("email", $email)->fetch();

        $section = $this->session->getSection(self::SESSION_SECTION);
        $section->setExpiration($expiration);

        if ($admin && $this->passwords->verify($password, $admin->password)) { // log in
            $accessLog = [
                AccessLog::ip => $_SERVER["REMOTE_ADDR"],
                AccessLog::time => new DateTime()
            ];
            $logRepository->insert($accessLog);
            $section['id'] = $admin->id;
        } else {
            throw new BadCredentialsException();
        }
    }

    public function getId(): ?int
    {
        $id = $this->session->getSection(self::SESSION_SECTION)['id'];
        if (!$id) return null;
        return $id;
    }

    /**
     * @return ActiveRow|null
     * @throws EntityNotFoundException
     */
    public function getUser(): ?ActiveRow
    {
        $id = $this->getId();
        return $id ? $this->adminRepository->findById($id) : null;
    }

    /**
     * @throws LoggedOutException
     */
    public function logout(): void
    {
        $section = $this->session->getSection(self::SESSION_SECTION);
        if (!$section['id']) throw new LoggedOutException();
        $section->remove();
    }

    public function getPasswords(): Passwords {
        return $this->passwords;
    }
}
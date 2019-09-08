<?php

namespace TailgateApi\Auth;

use OAuth2\Storage\Pdo;

// extend oauth2 PDO implementation since we need our implementations
class TailgatePDOStorage extends Pdo
{
    private $passwordHashing;
    
    public function __construct($db, $passwordHashing, $config = [])
    {
        $this->passwordHashing = $passwordHashing;
        parent::__construct($db, $config);
    }

    protected function checkPassword($user, $password)
    {
        return $this->passwordHashing->verify($password, $user['password_hash']);
    }

    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $stmt = $this->db->prepare(sprintf('
            SELECT * FROM %s
            WHERE client_id = :client_id', $this->config['client_table'])
        );
        $stmt->execute(compact('client_id'));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result && $this->passwordHashing->verify($client_secret, $result['client_secret']);
    }


    public function getUser($email)
    {
        $stmt = $this->db->prepare($sql = sprintf('SELECT * from %s where email=:email', $this->config['user_table']));
        $stmt->execute(['email' => $email]);

        if (!$userInfo = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return false;
        }

        // the default behavior is to use "email" as the user_id
        return array_merge(['user_id' => $email], $userInfo);
    }

    public function getDefaultScope($client_id = null)
    {
        return null;
    }
}
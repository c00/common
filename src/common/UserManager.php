<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 10:21
 */

namespace c00\common;
use c00\log\Log;

class UserManager 
{
    use TDependency;
    
    public $loggedIn = false;
    /**
     * @var null|User
     */
    public $user = null;
    /**
     * @var null| Session
     */
    public $session = null;

    public function __construct(){
    }

    /** Login a user
     * @param $email string The email address
     * @param $password string The password
     * @return bool|Session The session if the session/user is valid, false otherwise.
     */
    public function login($email, $password){
        $user = $this->dc->getDb()->getUserByEmail($email);

        if (!$user) {
            Log::warning("User not found: $email");
            return false;
        }

        if (!$user->checkPassword($password)) {
            Log::warning("Incorrect password for user $email");
            return false;
        }

        if (!$user->active){
            Log::info("User not active");
            return false;
        }

        $session = Session::newSession($user);
        if (!$this->dc->getDb()->saveSession($session)){
            Log::error("Error saving session");
            return false;
        }

        $this->user = $user;
        $this->session = $session;
        $this->loggedIn = true;

        return $session;
    }

    /** Checks if a session and user are valid and returns the session.
     * @param $token string The session token
     * @return bool|Session The session if the session/user is valid, false otherwise.
     */
    public function checkSession($token){
        $session = $this->dc->getDb()->getSession($token);

        if (!$session) {
            Log::debug("Session token not found.");
            return false;
        }

        if ($session->isExpired()){
            Log::debug("Session is expired");
            return false;
        }

        $user = $this->dc->getDb()->getUser($session->userId);

        if (!$user) {
            Log::error("User not found for ID {$session->userId}, session token {$session->token}");
            return false;
        }

        if (!$user->active){
            Log::info("User not active");
            return false;
        }

        $session->user = $user;

        $this->user = $user;
        $this->session = $session;
        $this->loggedIn = true;

        return $session;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 01:28
 */

namespace c00\common;
use c00\common\Helper as H;

class User implements IDatabaseObject
{
    public $id, $email, $firstName, $lastName, $password;
    public $isAdmin = false;
    public $active = true;
    /** @var CovleDate */
    public $created;

    /**
     * @var bool Indicates if the hash should be resaved.
     */
    private $rehashedPassword = false;

    public function __construct(){
    }

    public function newUser($email, $password, $firstName = null, $lastName = null){
        $u = new User();
        $u->email = $email;
        $u->setPassword($password);
        $u->firstName = $firstName;
        $u->lastName = $lastName;
        $u->created = CovleDate::now();

        return $u;
    }

    public function setPassword($password){
        if (strlen($password) > 72) return false;

        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return true;
    }

    public function checkPassword($password){
        if (strlen($password) > 72) return false;

        $check = password_verify($password, $this->password);

        if (!$check) return false;

        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)){
            $this->setPassword($password);
            $this->rehashedPassword = true;
        }
        
        return $check;
    }

    public function toArray(){
        $array = json_decode(json_encode($this) ,true);
        $array['created'] = $this->created->toSeconds();

        unset($array['domains']);

        return $array;
    }

    /**
     * @param $array array
     * @return bool|User
     */
    public static function fromArray($array){
        $u = new User();
        $u = H::objectFromArray($array, $u);

        $u->created = CovleDate::fromSeconds($u->created);

        $u->isAdmin = (bool)$u->isAdmin;

        return $u;
    }

    public function toShowable(){
        $u = $this->toArray();
        unset ($u['password']);
        $u['created'] = $this->created->toMiliSeconds();
        $u['active'] = (bool) $this->active;
        $u['isAdmin'] = (bool) $this->isAdmin;
        $a['id'] = (int) $this->id;

        return $u;
    }


}
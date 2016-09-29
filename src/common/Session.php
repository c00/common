<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 09:30
 */

namespace c00\common;

use c00\common\Helper as H;

class Session implements IDatabaseObject
{
    const EXPIRES_DAYS = 14;

    public $token, $userId;
    /** @var CovleDate */
    public $created;
    /** @var CovleDate */
    public $expires;
    /** @var CovleDate */
    public $renewed;
    /** @var CovleDate */
    public $notificationsChecked;

    /** @var User */
    public $user;

    public function __construct(){
    }

    public static function newSession(User $user){
        $s = new Session();
        $s->userId = $user->id;
        $s->user = $user;
        $s->notificationsChecked = CovleDate::fromSeconds(0);
        $s->created = CovleDate::now();
        $s->renewed = CovleDate::now();
        $s->expires = CovleDate::now()->addDays(self::EXPIRES_DAYS);
        $s->token = bin2hex(openssl_random_pseudo_bytes(32));

        return $s;
    }

    public function renewSession(){
        $this->renewed = CovleDate::now()->toSeconds();
        $this->expires = CovleDate::now()->addDays(self::EXPIRES_DAYS);
    }

    public function isExpired(){
        return ($this->expires->isEarlierThan(CovleDate::now()));
    }

    public function expireSession(){
        $this->expires = CovleDate::now()->addSeconds(-1);
    }

    public function toShowable(){
        $t = $this->toArray();
        $t['user'] = $this->user->toShowable();

        return $t;
    }

    public function toArray(){
        $array = json_decode(json_encode($this) ,true);
        unset ($array['user']);

        $array['expires'] = $this->expires->toSeconds();
        $array['created'] = $this->expires->toSeconds();
        $array['renewed'] = $this->expires->toSeconds();
        $array['notificationsChecked'] = $this->notificationsChecked->toSeconds();


        return $array;
    }

    public static function fromArray($array){
        /**
         * @var $s Session
         */
        $s = H::objectFromArray($array, self::class);

        $s->created = CovleDate::fromSeconds($s->created);
        $s->expires = CovleDate::fromSeconds($s->expires);
        $s->renewed = CovleDate::fromSeconds($s->renewed);
        $s->notificationsChecked = CovleDate::fromSeconds($s->notificationsChecked);

        return $s;

    }

}
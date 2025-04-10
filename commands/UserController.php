<?php


namespace app\commands;
use app\commands\DefaultController as Controller;
use app\helpers\Password;
use app\models\User;

class UserController extends Controller
{
    public function actionAddAdmin(){
        $user = new User();
        $user->username = 'leticia';
        $user->type = User::TYPE_ADMIN;
        $user->name ='Leticia';
        $user->email = 'leticia@grandejogo.org';
        $user->phone = 'xxxx';
        $user->password_hash = Password::hash("1234567@a");
        $user->blocked_at = null;
        $user->registration_ip = '127.0.0.1';
        $user->save();
    }
    public function actionChangePass($username){
        $user = User::findOne(['username' => $username]);
        if(!$user){
            return false;
        }
        $user->password_hash = Password::hash("1234567@a");
        $user->save();
    }
}
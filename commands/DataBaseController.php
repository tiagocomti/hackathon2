<?php


namespace app\commands;
use app\commands\DefaultController as Controller;
use app\helpers\Crypt;
use app\helpers\Date;
use app\models\Bases;
use app\models\Equipe;
use app\models\User;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

class DataBaseController extends Controller
{
    public function actionCryptPass(){
        $string = (BaseConsole::input("set a 32byte database pass: "));
        $pass = Crypt::easyEncrypt($string, Crypt::getOurSecret());
        $byte_array = unpack('C*', $pass);
        BaseConsole::output($this->ansiFormat("Paste it in your db conf:", BaseConsole::FG_GREEN));
        BaseConsole::output(json_encode($byte_array));
    }

    public function actionCheckDb(){
        BaseConsole::output("start at: ". Date::getTimeWithMicroseconds());
        User::findOne([true => true]);
        BaseConsole::output("base ok.");
        BaseConsole::output("end at: ". Date::getTimeWithMicroseconds());
    }

    public function actionImportEquipes($path){
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(Equipe::findOne(["name"=>trim($line)])){
                    continue;
                }
                $equipe = new Equipe();
                $equipe->name = trim($line);
                if($equipe->save()){
                    echo "Equipe ".$equipe->name." salva com sucesso, ID: ".$equipe->id;
                    echo "\n";
                }else{
                    echo "deu ruim, segue erro";
                    print_r($equipe->getErrors());
                }
            }
        }
        echo "finalizado";
    }

    public function actionImportBases($caminho){
        $handle = fopen($caminho, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(Bases::findOne(["name"=>trim($line)])){
                    continue;
                }
                $base = new Bases();
                $base->name = trim($line);
                if($base->save()){
                    echo "Base ".$base->name." salva com sucesso, ID: ".$base->id;
                    echo "\n";
                }else{
                    echo "deu ruim, segue erro";
                    print_r($base->getErrors());
                }
            }
        }
        echo "finalizado";
    }
}
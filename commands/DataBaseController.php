<?php


namespace app\commands;
use app\commands\DefaultController as Controller;
use app\helpers\Crypt;
use app\helpers\Date;
use app\helpers\Password;
use app\helpers\Strings;
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

    public function actionImportParticipantes($path){
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $explode = explode("|", $line);
                $nome = trim($explode[0]);
                $nome_equipe = trim($explode[1]);
                $telefone = trim($explode[2]);
                $password = trim($explode[3]);
                $password = ($password!=="")?$password:Password::generate(6, true);
                $user = User::findOne(["name"=>$nome, "username" =>  strtolower(Strings::removeEspecialCharacters($nome))]);
                if(!$user) {
                    $user = new User();
                    $user->phone = Strings::sanitizationPhone($telefone);
                    $user->password_hash = Password::hash($password);
                    $user->name = $nome;
                    $user->observacoes = "Monitor(a)";
                    $user->type = User::TYPE_PARTICIPANTE;
                    $user->username = strtolower(Strings::removeEspecialCharacters($nome));
                    $user->email = $user->username . "@" . "jogodacidade.app";
                }else{
                    $password = "A mesma de antes";
                }
                if($user->save()){
                    $equipe = Equipe::findOne(["name" => $nome_equipe]);
                    if(!$equipe){
                        echo "Equipe ".$nome_equipe." não encontrada";
                        echo "\n";
                        continue;
                    }
                    $equipe->users[] = $user->id;
                    if($equipe->save()){
                        echo "username: ".$user->username." - ";
                        echo "senha: ".$password." - ";
                        echo "base:" . $equipe->name;
                        echo "\n";
                    }else{
                        echo "Falha ao salvar equipe";
                        print_r($equipe->getErrors());
                    }
                }else{
                    echo "Falha ao salvar usuario: ". $user->name;
                    print_r($user->getErrors());
                }
            }
        }

    }

    public function actionImportAvaliadores($path){
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $explode = explode("|", $line);
                if(count($explode) < 2){continue;}
                $password = Password::generate(6, true);
                $name = trim($explode[0]);
                $base_name = trim($explode[1]);
                $telefone = trim($explode[2]);
                $user = User::findOne(["name"=>$name, "username" =>  strtolower(Strings::removeEspecialCharacters($name))]);
                if(!$user) {
                    $user = new User();
                    $user->name = $explode[0];
                    $user->username = strtolower(Strings::removeEspecialCharacters($name));
                    $user->phone = $telefone;
                    $user->password_hash = Password::hash($password);
                    $user->name = $name;
                    $user->observacoes = "Avaliador";
                    $user->type = User::TYPE_AVALIADOR;
                    $user->username = strtolower(Strings::removeEspecialCharacters($name));
                    $user->email = $user->username . "@" . "jogodacidade.app";
                }
                if($user->save()){
                    $base = Bases::findOne(["name" => $base_name]);
                    if(!$base){
                        echo "Equipe ".$base_name." não encontrada";
                        echo "\n";
                        continue;
                    }
                    $base->users[] = $user->id;
                    if($base->save()){
                        $telefone = Strings::sanitizationPhone($telefone);
                        echo "username: ".$user->username." - ";
                        echo "senha: ".$password." - ";
                        echo "base:" . $base->name." - ";
                        echo "whatsapp - https://api.whatsapp.com/send?phone=55".$telefone."&text=Ol%C3%A1%20".urlencode($name). "%2C%20voc%C3%AA%20foi%20registrado%20no%20nosso%20sistema%20na%20patrulha%20".urlencode($base_name)."%21%20Segue%20seus%20dados%20de%20acesso%20pro%20sistema%20de%20pontuacao%20do%20jogo%20da%20cidade%21%21%21%20LINK%3A%20https%3A%2F%2Fjogodacidade.app%2F%20com%20username%3A".urlencode($user->username)."%20e%20senha%3A%20".urlencode($password);
                        echo "\n";
                    }else{
                        echo "Falha ao salvar equipe";
                        print_r($base->getErrors());
                    }
                }else{
                    echo "Falha ao salvar usuario: ". $user->name;
                    print_r($user->getErrors());
                }
            }
        }

    }

    public function actionParser($path){
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(trim($line) !== ""){
                    $explode = explode("|", $line);
                    $acesso = Trim($explode[0]);
                    $telefone =Strings::sanitizationPhone(trim($explode[1]));
                    $whatsapp = "https://api.whatsapp.com/send?phone=55".$telefone."&text=Boa%20noite%20monitor%28a%29%21%20Bem-vindo%28a%29%20ao%20https%3A%2F%2Fjogodacidade.app.%20Seu%20acesso%20foi%20criado%20para%20monitorar%20a%20pontua%C3%A7%C3%A3o%20da%20sua%20patrulha%20e%20visualizar%20seu%20QRcode%20de%20uma%20forma%20facil%20e%20r%C3%A1pida.%20Acesse%20com%20esses%20dados%3A".urlencode($acesso);
                    echo $whatsapp;
                    echo "\n";
                    echo "\n";
                }
            }
        }
    }

    public function actionChangePass($username, $senha){
        $user = User::findOne(["username" => $username]);
        if($user){
            $user->password_hash = Password::hash($senha);
            var_dump($user->save());
            return ;
        }
        echo "Usuario nao encontrado";
    }
}
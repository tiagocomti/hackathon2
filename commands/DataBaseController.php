<?php


namespace app\commands;
use app\commands\DefaultController as Controller;
use app\helpers\Crypt;
use app\helpers\Date;
use app\helpers\Password;
use app\helpers\Strings;
use app\models\Bases;
use app\models\Equipe;
use app\models\Pontos;
use app\models\User;
use tiagocomti\cryptbox\Cryptbox;
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
        print_r(Cryptbox::decryptDBPass('{"1":52,"2":66,"3":112,"4":111,"5":69,"6":79,"7":47,"8":66,"9":55,"10":122,"11":86,"12":48,"13":49,"14":71,"15":106,"16":102,"17":101,"18":49,"19":107,"20":55,"21":120,"22":65,"23":97,"24":109,"25":57,"26":82,"27":100,"28":89,"29":75,"30":80,"31":105,"32":113,"33":47,"34":73,"35":71,"36":119,"37":78,"38":73,"39":86,"40":89,"41":67,"42":54,"43":83,"44":68,"45":54,"46":97,"47":84,"48":82,"49":104,"50":71,"51":104,"52":115,"53":112,"54":99,"55":43,"56":81,"57":65,"58":122,"59":120,"60":66,"61":70,"62":48,"63":67,"64":47}'));exit;
        BaseConsole::output("start at: ". Date::getTimeWithMicroseconds());
        User::findOne([true => true]);
        BaseConsole::output("base ok.");
        BaseConsole::output("end at: ". Date::getTimeWithMicroseconds());
    }

    public function actionImportEquipes($path){
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $linha = explode("-", trim($line));
                list($id, $patrulha, $monitor) = $linha;
                if(Equipe::findOne(["name"=>trim($patrulha)])){
                    continue;
                }
                $user = new User();
                $user->name = $monitor;
                $user->phone = "xxxx";
                $user->observacoes = "Monitor(a)";
                $user->type = User::TYPE_PARTICIPANTE;
                $user->username = strtolower(Strings::removeEspecialCharacters($monitor)) . Password::generate(4);
                $user->email = $user->username . "@" . "jogodacidade.app";
                $user->password_hash = Password::hash("jogodacidade@1234567a");
                if ($user->save()) {
                    echo "username: " . $user->username . " - ";
                    echo "base:" . $patrulha;
                    echo "user:" . $user->getId();
                    echo "\n";
                }else{
                    print_r($user->getErrors());
                }

                $equipe = new Equipe();
                $equipe->id = trim((int)$id);
                $equipe->name = trim($patrulha);
                $equipe->users = [$user->getId()];
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
                $base_explode = explode("|", $line);
                $name_user = trim($base_explode[1]);
                if(User::findOne(["name"=>trim($base_explode[1])])){
                    continue;
                }
                $user = new User();
                $user->name = $name_user;
                $user->phone = "xxxx";
                $user->observacoes = "Avaliador de base";
                $user->type = User::TYPE_AVALIADOR;
                $user->username = strtolower(Strings::removeEspecialCharacters($name_user));
                $user->email = $user->username . "@" . "jogodacidade.app";
                $user->password_hash = Password::hash($pass = Password::generate(4));
                if ($user->save()) {
                    echo "username: " . $user->username . " - ";
                    echo "user:" . $user->getId();
                    echo "senha:" . $pass;
                    echo "\n";
                }else{
                    print_r($user->getErrors());
                }
                if(Bases::findOne(["name"=>trim($base_explode[0])])){
                    continue;
                }
                $base = new Bases();
                $base->name = trim($base_explode[0]);
                $base->ramo = "senior";
                $base->users = [$user->getId()];
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

    public function actionImportParticipantes($path)
    {
        $handle = fopen($path, "r");
        $ramo = "";
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $ramo_explode = explode("----", $line);
                if (count($ramo_explode) > 1) {
                    $ramo = trim($ramo_explode[1]);
                }
                $explode = explode("|", $line);
                if (count($explode) > 1) {
                    if ($ramo == Equipe::RAMO_LOBO) {
                        $equipe_nome = "(GE: " . trim($explode[0]) . "° - " . trim($explode[1]) . ") ";
                        $nome = trim($explode[2]);
                        $participante = trim($explode[3]);
                    } else {
                        $equipe_nome = "(GE: " . trim($explode[0]) . "°) ";
                        $nome = $explode[1];
                        $participante = trim($explode[2]);
                    }
                    $equipe = new Equipe();
                    $equipe->ramo = $ramo;
                    $equipe->name = $equipe_nome.$nome;

                    $user = new User();
                    $user->name = $participante;
                    $user->phone = "xxxx";
                    $user->observacoes = "Monitor(a)";
                    $user->type = User::TYPE_PARTICIPANTE;
                    $user->username = strtolower(Strings::removeEspecialCharacters($nome)) . Password::generate(4);
                    $user->email = $user->username . "@" . "jogodacidade.app";
                    $user->password_hash = Password::hash("jogodacidade@1234567a");
                    if ($user->save()) {
                        $equipe->users[] = $user->id;
                        if ($equipe->save()) {
                            echo "username: " . $user->username . " - ";
                            echo "base:" . $equipe->name;
                            echo "user:" . $user->getId();
                            echo "\n";
                        }else{
                            print_r($equipe->getErrors());
                        }
                    }else{
                        print_r($user->getErrors());
                    }

                }
            }
        }
        echo "finalizado";
    }

    public function actionCheckPoints($path, $base_id)
    {
        $base_id = trim($base_id);
        $base = Bases::findOne(["id"=>$base_id]);
        if(!$base){
            echo "Base nao encontrada";exit;
        }
        $handle = fopen($path, "r");
        $ramo = "";
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $ramo_explode = explode("----", $line);
                if (count($ramo_explode) > 1) {
                    $ramo = trim($ramo_explode[1]);
                }
                $explode = explode("|", $line);
                if (count($explode) > 1) {
                    if ($ramo == Equipe::RAMO_LOBO) {
                        $equipe_nome = "(GE: " . trim($explode[0]) . "° - " . trim($explode[1]) . ") ";
                        $nome = trim($explode[2]);
                        $pontos = trim($explode[3]);
                    } else {
                        $equipe_nome = "(GE: " . trim($explode[0]) . "°) ";
                        $nome = $explode[1];
                        $pontos = trim($explode[2]);
                    }
                    $equipe = Equipe::findOne(["name"=>$equipe_nome.$nome]);
                    if($equipe){
                        $ponto = Pontos::find()->andWhere(["equipe_id"=>$equipe->id, "base_id" => $base->id])->one();
                        if(!$ponto){
                            $ponto = new Pontos();
                            $ponto->base_id = $base->id;
                            $ponto->equipe_id = $equipe->id;
                            $ponto->is_base = true;
                            $ponto->avaliador_id = $base->avaliadores[0]->id;
                        }
                        $ponto->pontos = $pontos;
                        if(!$ponto->save()){
                            print_r($ponto->getErrors());
                        }else{
                            echo "equipe: ".$equipe_nome.$nome." alterada com todo sucesso para base: ".$base->name;
                            echo "\n";
                        }
                    }else{
                        echo "equipe: ".$equipe_nome.$nome." não encontrada :(";
                        echo "\n";
                    }
                }
            }
        }
        echo "finalizado";
    }

    public function actionImportAvaliadores($path)
    {
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $explode = explode("|", $line);
                if (count($explode) < 2) {
                    continue;
                }
                $password = Password::generate(6, true);
                $name = trim($explode[0]);
                $base_name = trim($explode[1]);
                $telefone = trim($explode[2]);
                $user = User::findOne(["name" => $name, "username" => strtolower(Strings::removeEspecialCharacters($name))]);
                if (!$user) {
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
                if ($user->save()) {
                    $base = Bases::findOne(["name" => $base_name]);
                    if (!$base) {
                        echo "Equipe " . $base_name . " não encontrada";
                        echo "\n";
                        continue;
                    }
                    $base->users[] = $user->id;
                    if ($base->save()) {
                        $telefone = Strings::sanitizationPhone($telefone);
                        echo "username: " . $user->username . " - ";
                        echo "senha: " . $password . " - ";
                        echo "base:" . $base->name . " - ";
                        echo "whatsapp - https://api.whatsapp.com/send?phone=55" . $telefone . "&text=Ol%C3%A1%20" . urlencode($name) . "%2C%20voc%C3%AA%20foi%20registrado%20no%20nosso%20sistema%20na%20patrulha%20" . urlencode($base_name) . "%21%20Segue%20seus%20dados%20de%20acesso%20pro%20sistema%20de%20pontuacao%20do%20jogo%20da%20cidade%21%21%21%20LINK%3A%20https%3A%2F%2Fjogodacidade.app%2F%20com%20username%3A" . urlencode($user->username) . "%20e%20senha%3A%20" . urlencode($password);
                        echo "\n";
                    } else {
                        echo "Falha ao salvar equipe";
                        print_r($base->getErrors());
                    }
                } else {
                    echo "Falha ao salvar usuario: " . $user->name;
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
                    $explode = explode("-", $line);
                    list($user, $senha, $telefone) = $explode;
                    $telefone =Strings::sanitizationPhone(trim($telefone));
                    $whatsapp = "https://api.whatsapp.com/send?phone=55".$telefone."&text=Ol%C3%A1%2C%20boa%20tarde.%0A%0AAqui%20%C3%A9%20o%20Tiago%20da%20FreeBSD%20Brasil%2C%20segue%20sua%20senha%20de%20digest.%20O%20seu%20login%20foi%20enviado%20por%20outro%20canal%20de%20comunica%C3%A7%C3%A3o%0A%0ASenha%3A".urlencode($senha);
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

    public function actionCreateUserByBase(){
        /** @var Bases[] $bases */
        $bases = Bases::find()->all();
        foreach($bases as $base){
            $explode_name = explode(" ",$base->name);
            if(count($explode_name)>=2){
                $name = $explode_name[0]."_".$explode_name[1]."_".$explode_name[2];
            }else{
                $name = $explode_name[0]."_".$base->ramo;
            }
            $password = Password::generate(5);
            $name = Strings::removeEspecialCharacters(strtolower($name));
            $user = new User();
            $user->name = $name;
            $user->username = $name;
            $user->email = $name."@jogodacidade.app";
            $user->password_hash = Password::hash($password);
            $user->type = User::TYPE_AVALIADOR;
            $user->phone = "xxxxx";
            if($user->save()){
                $base->users[] = $user->id;
                if(!$base->save()){
                    print_r($base->getErrors());
                }
            }else{
                $user->getErrors();
            }
            $ramo = ($base->ramo)??"Comum";
            echo "Nome da base: (".$base->name.") | login: (".$user->username.")  | senha: (".$password.") | Ramo: (".$ramo.")";
            echo "\n";
        }
    }

    public function actionGenerateResult(){
        $model = Equipe::find()->andWhere(["ramo" => Equipe::RAMO_SENIOR])->all();
        /**
         * @var  $chave
         * @var Equipe $equipe
         *
         */
        foreach($model as $chave => $equipe){
            $ponto_passaporte = Pontos::find()->andWhere(["equipe_id" => $equipe->id,"base_id"=>100])->all();
            $entrega = ($ponto_passaporte!=null)?"sim":"não";
            $equipes[$chave] = $equipe->getAttributes(["name","ramo"]);
            $equipes[$chave]["pontos_totais"] = $equipe->getPontos();
            $equipes[$chave]["entregou_passaporte"] = $entrega;
        }
        usort($equipes, function($a, $b) {
            return $a['pontos_totais'] <=> $b['pontos_totais'];
        });
        $equipes = array_reverse($equipes);
        foreach ($equipes as $equipe){
            echo implode(",",$equipe);
            echo "\n";
        }

//        print_r($equipes);
    }

    public function actionDeletePonto($id){
        Pontos::deleteAll(["id"=>$id]);
    }
}
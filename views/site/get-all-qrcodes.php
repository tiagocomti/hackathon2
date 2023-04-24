<?php
/**
 * @var \app\models\Equipe[] $equipes
 */

foreach ($equipes as $equipe){?>
    Equipe: <?=$equipe["name"]?> [Ramo: <?=$equipe["ramo"]?>] ID: <?=$equipe["id"]?> <br/>
    <img src="<?=$equipe["base_64"]?>" alt=""/><br/>
<?php }
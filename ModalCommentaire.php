<?php
declare(strict_types=1);
require_once "autoload.php";

use \data\Tache;

if(!isset($_POST['task_id'])){
    throw new Exception("");
}

$task_id = (int)$_POST['task_id'];

$tache = Tache::createFromId($task_id);

$html = <<<HTML
    <div class="bg-task bg-light m-2 p-2 border-large">
        <div class="p-2 bg-transparent-lightgray">
            <h2>Commentaires:</h2>
            <h5>{$tache->getNomTache()}</h5>
            <!-- Conteneur des commentaires -->
            <hr>
HTML;

foreach ($tache->getComments() as $commentaire) {
    $cc = new CommentaireComponent($commentaire);
    $html .= $cc->toHTML();
}

$html .= <<<HTML
        </div>
        <div
            class="d-flex flex-row participant shadow justify-content-center align-items-center rounded-pill bg-white m-2 p-2">
            <!-- + -->
            <i class="bi bi-plus-lg"></i>
        </div>
    </div>
HTML;

echo $html;
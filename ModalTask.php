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
        <!-- Conteneur nom + icones chgt & profil -->
        <div class="d-flex justify-content-between">
            <div class="flex-grow-1 font-medium">
                <!-- Nom de la tâche-->
                Modification de tâche
            </div>
            <!-- Rond + lettre -->
            <div class="d-flex align-content-start">
HTML;

$pgc = new ProfilGroupComponent(...$tache->getAssignees());
$html .= $pgc->toHTML();

$html .= <<<HTML
            </div>
        </div>

        <!-- Conteneur Formulaire description-->
        <!-- Formulaire -->
        <form name="description&couleurs">
            <div class="d-flex flex-column">
                <label for="name">Nom de tâche</label>
                <input class="form-control mb-2" type="text" id="name" value="{$tache->getNomTache()}" name="name" placeholder="Nouveau nom de tâche">

                <label for="description">Description de la tâche</label>
                <textarea class="form-control mb-2" type="text" id="description" rows="3" name="description" placeholder="Description de la tâche">{$tache->getDescTache()}</textarea>
HTML;

$egc = new EtiquetteGroupComponent(...$tache->getTags());
$html .= $egc->toHTML();

$html .= <<<HTML
                <div class="d-flex flex-grow-1 justify-content-between ">
                    <button class="button w-75 bg-success m-1" style="color: white;" type="submit">Sauvegarder</button>
                    <button class="button w-25 bg-danger m-1" style="color: white;" type="reset">Supprimer</button>
                </div>
            </div>
        </form>
    </div>
HTML;

echo $html;
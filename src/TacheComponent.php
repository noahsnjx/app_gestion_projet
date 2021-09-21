<?php


use data\Tache;

class TacheComponent extends Component
{
    private Tache $tache;

    public function __construct(Tache $tache){
        parent::__construct();
        $this->tache = $tache;
    }


    public function toHTML(): string{
        $html =<<<HTML
           <!-- Composant tâche-->
        <div class="bg-task grow-hover border-medium m-2 p-2">
            <!-- Conteneur nom + icones chgt & profil -->
            <div class="d-flex justify-content-between">
                <div class="flex-grow-1 d-flex flex-column">
                    <!-- Nom de la tâche-->
                    <div>
                        <button type="button" class="task-modifier btn btn-secondary btn-sm" data-taskid="{$this->tache->getidTache()}">
                            <i class="bi bi-pencil-fill text-white"></i>
                        </button>
                        {$this->tache->getNomTache()}
                        <!--Image description + crayon -->
                    </div>
                    <div class="description"><i class="bi bi-info-circle-fill"></i> {$this->tache->getDescTache()}</div>
                </div>
HTML;

        $pgc = new ProfilGroupComponent(...$this->tache->getAssignees());
        $html .= $pgc->toHTML();

        $html.= <<<HTML
            </div>
            <!--Conteneur tags & coms-->
            <div class="d-flex justify-content-between">
HTML;

        $egc = new EtiquetteGroupComponent(...$this->tache->getTags());
        $html .= $egc->toHTML();

        $cc = new CommentaireCountComponent($this->tache);
        $html .= $cc->toHTML();

        $html .= <<<HTML
            </div>
        </div>
HTML;
                    return $html;
    }
}
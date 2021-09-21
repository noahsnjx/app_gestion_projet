<?php

use data\Commentaire;

class CommentaireComponent extends Component
{
    private Commentaire $commentaire;

    public function __construct(Commentaire $commentaire){
        $this->commentaire = $commentaire;
        parent::__construct();
    }

    public function toHTML(): string
    {
        $html = <<<HTML
        <div class="d-flex flex-column  border-large shadow bg-white m-2 p-2">
            <div class="d-flex flex-column">
                    <div class="d-flex flex-row align-items-center">
                        <!-- Rond + lettre -->
HTML;

        $pc = new ProfilComponent($this->commentaire->getUtilisateur());
        $html .= $pc->toHTML();

        $html .= <<<HTML
                        <!-- Nom du membre -->
                        <div class="d-flex flex-column">
                            <div class="text-start m-1">
HTML;

        $user = $this->commentaire->getUtilisateur();
        $html .= "{$user->getNom()} {$user->getPnom()}";

        $html .= <<<HTML
                            </div>
                            <div class="date-format">{$this->commentaire->getDateCom()}</div>
                        </div>
                        <i class="btn btn-danger m-2 border-2 py-0 px-1 text-white bi bi-trash"></i>
                    </div>
                    <!-- commentaires -->
                    <div class="d-flex flex-row border-medium bg-transparent-gray flex-grow-1 m-2 p-2">
                        {$this->commentaire->getContenuCom()}
                    </div>
                </div>
HTML;

        foreach ($this->commentaire->getSubComments() as $subComment) {
            $scc = new CommentaireSubComponent($subComment);
            $html .= $scc->toHTML();
        }

        $html .= <<<HTML
            <div class="d-flex flex-row justify-content-end">
                <div class="d-flex button w-50 justify-content-center align-items-end bg-transparent-gray m-2 p-2">
                    <!-- + -->
                    <i class="bi bi-plus-lg"></i>
                </div>
            </div>
        </div>
HTML;

        return $html;
    }
}
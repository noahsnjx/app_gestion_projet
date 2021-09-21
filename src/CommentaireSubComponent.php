<?php

use data\Commentaire;

class CommentaireSubComponent extends Component
{
    private Commentaire $commentaire;

    public function __construct(Commentaire $commentaire){
        $this->commentaire = $commentaire;
        parent::__construct();
    }

    public function toHTML(): string
    {
        $html = <<<HTML
            <div class="d-flex flex-column justify-content-end">
                <div class="d-flex flex-row-reverse align-items-center">
                    <!-- Rond + lettre -->
HTML;

        $pc = new ProfilComponent($this->commentaire->getUtilisateur());
        $html .= $pc->toHTML();

        $html .= <<<HTML
                    <!-- Nom du membre -->
                    <div class="d-flex flex-column align-items-end">
                        <div>
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
                <div class="d-flex ms-auto flex-row comment-right border-medium bg-transparent-gray flex-grow-1 m-2 p-2">
                    {$this->commentaire->getContenuCom()}
                </div>
            </div>
HTML;

        return $html;
    }
}
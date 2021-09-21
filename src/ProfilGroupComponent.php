<?php


use data\Tache;
use data\Utilisateur;

class ProfilGroupComponent extends Component
{
    public function __construct(Utilisateur ...$users){
        parent::__construct();
        foreach ($users as $user) {
            $this->addComponent(new ProfilComponent($user));
        }
    }

    public function toHTML(): string{
        $html =<<<HTML
        <div class="d-flex align-content-start chain-profile">
            {$this->componentsToHTML()}
        </div>
HTML;
        return $html;
    }
}
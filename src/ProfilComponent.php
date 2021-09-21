<?php


use data\Utilisateur;

class ProfilComponent extends Component
{

    private Utilisateur $user;

    public function __construct(Utilisateur $user)
    {
        $this->user = $user;
        parent::__construct();
    }

    public function toHTML(): string
    {
        $pastille=strtoupper($this->user->getPnom()[0]);
        return <<<HTML
            <div class="profile bg-primary text-white">{$pastille}</div>
HTML;

    }
}
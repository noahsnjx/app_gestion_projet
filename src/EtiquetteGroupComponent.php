<?php


use data\Etiquette;
use data\Tache;

class EtiquetteGroupComponent extends Component
{
    public function __construct(Etiquette ...$etiquettes){
        parent::__construct();
        foreach ($etiquettes as $etiquette) {
            $this->addComponent(new EtiquetteComponent($etiquette));
        }
    }

    public function toHTML(): string{
        $html =<<<HTML
        <div class="d-flex flex-wrap">
            {$this->componentsToHTML()}
        </div>
HTML;
        return $html;
    }
}
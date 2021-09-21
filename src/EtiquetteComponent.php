<?php


use data\Etiquette;

class EtiquetteComponent extends Component
{
    private Etiquette $etiquette;

    public function __construct(Etiquette $etiquette)
    {
        $this->etiquette = $etiquette;
        parent::__construct();
    }

    public function toHTML(): string
    {
        return <<<HTML
            <div class="tag bg-tag-dev shadow">{$this->etiquette->getNomTag()}</div>
HTML;

    }
}
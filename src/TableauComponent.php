<?php


use data\Tableau;

class TableauComponent extends Component{


    private Tableau $tableau;

    public function __construct(Tableau $tableau)
    {
        $this->tableau = $tableau;
        parent::__construct();
    }

    public function toHTML(): string{
       $html = <<<HTML
        <div class="categories d-flex flex-column flex-md-row align-content-start">
 HTML;
        foreach ($this->tableau->getCategories() as $category) {
            $cc = new CategorieComponent($category);
            $html .= $cc->toHTML();
        }

        $html .= <<<HTML
        </div>
HTML;
        return $html;
    }
}
<?php

use data\Categorie;

class CategorieComponent extends Component{

    private Categorie $categorie;

    public function __construct(Categorie $categorie){
        parent::__construct();
        $this->categorie = $categorie;
    }

    public function toHTML(): string{
        $html=<<<HTML
            <div class="category bg-category border-large d-flex flex-column slide-in-fade">
                <!-- En tête -->
                <div class="bg-category-head border-large d-flex flex-row justify-content-between">
                    <i class="bi bi-check-circle m-2"></i>
                    <p contenteditable="true" class="single-line m-2">{$this->categorie->getNomCat()}</p>
                    <i class="bi bi-pencil-fill m-2"></i>
                </div>
                <div class="scrollable-y">
HTML;

        foreach ($this->categorie->getTasks() as $task) {
            $tc = new TacheComponent($task);
            $html .= $tc->toHTML();
        }

        $html.=<<<HTML
                </div>

                <div class="m-2 d-flex participant shadow justify-content-center rounded-pill bg-white p-2">
                    <!-- + -->
                    <i class="bi bi-plus-lg"></i>
                </div>
            </div>
HTML;
        return $html;
    }
}
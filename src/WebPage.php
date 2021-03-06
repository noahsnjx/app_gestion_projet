<?php
declare(strict_types=1);

class WebPage{

    private String $head = "";
    private String $title = "";
    private String $body = "";
    private array $component;


    public function __construct(String $title = ""){
        $this->title = $title;
        $this->component = array();

    }

    /**
     *
     * @return String
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * add Component from list
     *
     * @param Component $component
     */

    public function addComponent(Component $component){
        $this->component[] = $component;
    }



    /**
     * @return String
     */
    public function getHead(): string
    {
        return $this->head;
    }

    /**
     * @return String
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * @param String $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Ajouter un contenu dans $this->head.
     * @param String $content
     */
    public function appendToHead(String $content): void{
        $this->head .= $content;
    }

    /**
     *
     * Ajouter un contenu CSS dans $this->head.
     * @param String $css
     */
    public function appendCss(String $css) : void{
        $this->head .= <<<HTML
            <style>$css</style>
HTML;

    }

    /**
     * Ajouter l'URL d'un script CSS dans $this->head.
     * @param String $url
     * @param String $integrity
     */
    public function appendCssUrl(String $url, string $integrity="") : void{
        $this->head .= <<<HTML
            <link rel="stylesheet" href="$url" integrity="$integrity">
HTML;
    }

    /**
     * Ajouter un contenu JavaScript dans $this->head.
     * @param String $js
     */

    public function appendJs(String $js) : void{
        $this->head .= <<<HTML
            <script>$js</script>
HTML;
    }


    /**
     * Ajouter l'URL d'un script JavaScript dans $this->head.
     * @param String $url
     */
    public function appendJsUrl(String $url) : void{
        $this->head .= <<<HTML
            <script src="$url"></script>
HTML;
    }

    /**
     * Ajouter un contenu dans $this->body.
     * @param String $content
     */
    public function appendContent(String $content) : void{
        $this->body .= $content;
    }

    /**
     * Produire la page Web compl??te.
     * @return String
     */
    public function toHTML() : String{
        $html = <<<HTML
        <!doctype html>
        <html lang="en">
            <head>
                <title>{$this->title}</title>
                {$this->head}
        
            </head>
                <body>
                    $this->body
HTML;

       foreach ($this->component as $component) {
           $html .= $component->toHTML();
       }

        $html .= <<< HTML
                </body>
            
        </html>
HTML;
        return $html;
    }

    /**
     *
     * Donner la date et l'heure de la derni??re modification du script principal.
     * @return string
     */
    public function getLastModification() : String{
        return date ("F d Y H:i:s.", getlastmod());
    }

    public function escapeString(String $string) : String {
        return htmlspecialchars($string,ENT_QUOTES | ENT_HTML5);
    }
}

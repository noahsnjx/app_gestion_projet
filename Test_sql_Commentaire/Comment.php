<?php
require_once "MyPDO.php";

class Comment
{
    private string $nbcom;

    public function __construct(int $idtache)
    {
        $stmt=MyPDO::getInstance()->prepare(<<<SQL
SELECT COUNT(idCom) as "nbcom"
FROM CommentaireComponent c
RIGHT JOIN TacheComponent t ON (c.idTache=t.idTache)
WHERE t.idTache=?

SQL
        );
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $stmt->execute([$idtache]);
        $this->nbcom=$stmt->fetch()[0];

    }

    public function toHtml():string
    {
        $result=<<<HTML
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Questrial">
</head>
<div>
    <div>
        <button type="button" class="btn btn-secondary" onclick="">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-right-text-fill" viewBox="0 0 16 16">
                <path d="M16 2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h9.586a1 1 0 0 1 .707.293l2.853 2.853a.5.5 0 0 0 .854-.353V2zM3.5 3h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1 0-1zm0 2.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1 0-1zm0 2.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1z"></path>
            </svg>
        </button>
        {$this->nbcom}
    </div>
</div>
HTML;
    return $result;
    }

    public function generatePageCom(int $idtache)
    {
        $stmt=MyPDO::getInstance()->prepare(<<<SQL
    SELECT contenuCom, dateCom
    FROM CommentaireComponent c
    RIGHT JOIN TacheComponent t ON (c.idTache=t.idTache)
    WHERE t.idTache=?
    
    SQL
        );
        $stmt->setFetchMode(PDO::FETCH_ASSOC).
        $stmt->execute([$idtache]);
        $html="";
        foreach($stmt->fetchAll() as $commentaire)
        {
            $html.=<<<HTML
    <div>
        {$commentaire['contenuCom']}  {$commentaire['dateCom']}
    </div>
    HTML;
        }
        return $html;
    }

}


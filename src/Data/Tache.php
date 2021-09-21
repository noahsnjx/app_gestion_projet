<?php
declare(strict_types=1);

namespace data;

class Tache implements IWritableEntity
{
    /**
     * @var int Identifiant de la tâche
     */
    private int $idTache;

    /**
     * @var int indentifiant du type de la tâche
     */
    private int $idTypeTache;

    /**
     * @var int identifiant de la catégorie
     */
    private int $idCat;

    private array $tags;

    private array $comments;

    private array $subTasks;

    private array $assignees;

    /**
     * @var string nom de la tâche
     */
    private string $nomTache;

    /**
     * @var string description de la tâche
     */
    private string $descTache;

    /**
     * @var int position de la tâche dans la liste
     */
    private int $ordreTache;

    /**
     * @var string date du début de la tâche
     */
    private string $dateDebTache;

    /**
     * @var string date de la fin de la tâche
     */
    private string $dateFinTache;

    /**
     * @var string statut de la tâche
     */
    private string $statut;


    public static function createFromId(int $idUserTask): Tache
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Tache
    WHERE idTache = :idTache
SQL
        );
        $request->bindParam('idTache', $idUserTask);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        $task = $request->fetch();

        return $task;
    }

    public static function fetchSubTasks(int $idTask): array
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT idTask
    FROM Tache
    WHERE idTache IN (
        SELECT idTache
        FROM Necessiter
        WHERE idTacheParent = :idTache
    )
SQL
        );
        $request->bindParam('idTache', $idTask);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();
        $tasks = [];
        foreach ($request->fetchAll() as $id) {
            $tasks[] = Tache::createFromId($id);
        }

        return $tasks;
    }

    public static function fetchTasks(int $idCat): array
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Tache
    WHERE idCat = :idCat
SQL
        );
        $request->bindParam('idCat', $idCat);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        return $request->fetchAll();
    }

    /**
     * @return int
     */
    public function getIdTypeTache(): int
    {
        return $this->idTypeTache;
    }

    /**
     * @param int $idTypeTache
     */
    public function setIdTypeTache(int $idTypeTache): void
    {
        $this->idTypeTache = $idTypeTache;
    }

    /**
     * @return int
     */
    public function getIdCategory(): int
    {
        return $this->idCat;
    }

    /**
     * @return string
     */
    public function getNomTache(): string
    {
        return $this->nomTache;
    }

    /**
     * @param string $nomTache
     */
    public function setNomTache(string $nomTache): void
    {
        $this->nomTache = $nomTache;
    }

    /**
     * @return string
     */
    public function getDescTache(): string
    {
        return $this->descTache;
    }

    /**
     * @param string $descTache
     */
    public function setDescTache(string $descTache): void
    {
        $this->descTache = $descTache;
    }

    /**
     * @return int
     */
    public function getOrdreTache(): int
    {
        return $this->ordreTache;
    }

    /**
     * @param int $ordreTache
     */
    public function setOrdreTache(int $ordreTache): void
    {
        $this->ordreTache = $ordreTache;
    }

    /**
     * @return string
     */
    public function getDateDebTache(): string
    {
        return $this->dateDebTache;
    }

    /**
     * @param string $dateDebTache
     */
    public function setDateDebTache(string $dateDebTache): void
    {
        $this->dateDebTache = $dateDebTache;
    }

    /**
     * @return string
     */
    public function getDateFinTache(): string
    {
        return $this->dateFinTache;
    }

    /**
     * @param string $dateFinTache
     */
    public function setDateFinTache(string $dateFinTache): void
    {
        $this->dateFinTache = $dateFinTache;
    }

    /**
     * @return int
     */
    public function getIdCat(): int
    {
        return $this->idCat;
    }

    /**
     * @param int $idCat
     */
    public function setIdCat(int $idCat): void
    {
        $this->idCat = $idCat;
    }

    /**
     * @return array
     */
    public function getAssignees(): array
    {
        if(!isset($this->assignees))
        {
            $this->assignees = Utilisateur::fetchAssignees($this->idTache);
        }
        return $this->assignees;
    }

    public function __destruct()
    {
        $this->registerOrUpdate();
    }

    /**
     * @return string
     */
    public function getStatut(): string
    {
        return $this->statut;
    }

    /**
     * @param string $statut
     */
    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    /**
     * @param int $idCat
     */
    public function setCat(int $category): void
    {
        $this->idCat = $category;
    }

    public function changeCategory(Categorie $cat, int $order): void
    {
        Categorie::createFromId($this->idCat)->removeTask($this);
        $this->idCat = $cat->getIdCat();
        $cat->addTask($this, $order);
    }

    public function isAvailable(): bool
    {
        if(!isset($this->subTasks))
        {
            $this->subTasks = Tache::fetchSubTasks($this->idTache);
        }

        $isValid = true;
        foreach ($this->subTasks as $subTask) {
            if ($subTask->status != 'T') {
                $isValid = false;
                break;
            }
        }
        return $isValid;
    }

    public function addSubTask(Tache $task): void
    {
        if(!isset($this->subTasks))
        {
            $this->subTasks = Tache::fetchSubTasks($this->idTache);
        }

        $request = \MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO Necessiter(idTache, idTacheParent)
                VALUES (:idTache, :idTacheParent))
SQL
        );
        $request->bindValue("idTache", $task->getidTache());
        $request->bindValue("idTacheParent", $this->idTache);
        $request->execute();

        $this->subTasks[] = $task;
    }

    /**
     * @return int
     */
    public function getidTache(): int
    {
        return $this->idTache;
    }

    /**
     * @param int $idTache
     */
    public function setidTache(int $idTache): void
    {
        $this->idTache = $idTache;
    }

    public function removeSubTask(Tache $tache): bool
    {
        if(!isset($this->subTasks))
        {
            $this->subTasks = Tache::fetchSubTasks($this->idTache);
        }

        $i = 0;
        while ($i < count($this->subTasks) && $this->subTasks[$i] != $tache) {
            $i++;
        }
        $res = $i < count($this->subTasks);
        if ($res) {
            $this->subTasks = array_splice($this->subTasks, $i, 1);

            $request = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Affecter
            WHERE idTache = :idTache AND idTacheParent = :idTacheParent
SQL
            );
            $request->bindValue("idTache", $tache->getidTache());
            $request->bindValue("idTacheParent", $this->idTache);
            $request->execute();
        }
        return $res;
    }

    public function getSubTasks(): array
    {
        if(!isset($this->subTasks))
        {
            $this->subTasks = Tache::fetchSubTasks($this->idTache);
        }
        return $this->subTasks;
    }

    public function addComment(Commentaire $comment): void
    {
        if(!isset($this->comments))
        {
            $this->comments = Commentaire::fetchTasksComments($this->idTache);
        }

        $this->comments[] = $comment;
    }

    public function removeComment(Commentaire $comment): bool
    {
        if(!isset($this->comments))
        {
            $this->comments = Commentaire::fetchTasksComments($this->idTache);
        }

        $i = 0;
        while ($i < count($this->comments) && $this->comments[$i] != $comment) {
            $i++;
        }
        $res = $i < count($this->comments);
        if ($res)
            $this->comments = array_splice($this->comments, $i, 1);
        return $res;
    }

    public function getComments(): array
    {
        if(!isset($this->comments))
        {
            $this->comments = Commentaire::fetchTasksComments($this->idTache);
        }

        return $this->comments;
    }

    public function addTag(Etiquette $tag): void
    {
        if(!isset($this->comments))
        {
            $this->comments = Etiquette::fetchTaskTags($this->idTache);
        }

        $request = \MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO Etiquetage(idTache, idTacheParent)
                VALUES (:idTache, :idTacheParent))
SQL
        );
        $request->bindValue("idTache", $tag->getIdTache());
        $request->bindValue("idTacheParent", $this->idTache);
        $request->execute();

        $this->tags[] = $tag;
    }

    public function removeTag(Etiquette $tag): bool
    {
        if(!isset($this->comments))
        {
            $this->comments = Etiquette::fetchTaskTags($this->idTache);
        }

        $i = 0;
        while ($i < count($this->tags) && $this->tags[$i] != $tag) {
            $i++;
        }
        $res = $i < count($this->tags);
        if ($res) {
            $this->tags = array_splice($this->tags, $i, 1);

            $request = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Affecter
            WHERE idTache = :idTache AND idTacheParent = :idTacheParent
SQL
            );
            $request->bindValue("idTache", $tag->getIdTache());
            $request->bindValue("idTacheParent", $this->idTache);
            $request->execute();
        }
        return $res;
    }

    public function getTags(): array
    {
        if(!isset($this->comments))
        {
            $this->tags = Etiquette::fetchTaskTags($this->idTache);
        }

        return $this->tags;
    }

    public function assignUser(Utilisateur $user): void
    {
        if(!isset($this->assignees))
        {
            $this->assignees = Utilisateur::fetchAssignees($this->idTache);
        }

        $request = \MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO Affecter(idUser, idTag)
                VALUES (:idUser, :idTag))
SQL
        );
        $request->bindValue("idUser", $user->getId());
        $request->bindValue("idTag", $this->idTache);
        $request->execute();

        $this->assignees[] = $user;
    }

    public function removeUser(Utilisateur $user): bool
    {
        if(!isset($this->assignees))
        {
            $this->assignees = Utilisateur::fetchAssignees($this->idTache);
        }

        $request = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Affecter
            WHERE idUser = :idUser AND idTag = :idTag
SQL
        );
        $request->bindValue("isUser", $user->getId());
        $request->bindValue("idTag", $this->idTache);
        $request->execute();

        $i = 0;
        while ($i < count($this->assignees) && $this->assignees[$i] != $user) {
            $i++;
        }
        $res = $i < count($this->assignees);
        if ($res) {
            $this->assignees = array_splice($this->assignees, $i, 1);

            $request = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Affecter
            WHERE idUser = :idUser AND idTag = :idTag
SQL
            );
            $request->bindValue("isUser", $user->getId());
            $request->bindValue("idTag", $this->idTache);
            $request->execute();
        }
        return $res;
    }

    public function registerOrUpdate(): void
    {
        if (!isset($this->idTache)) {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO Tache(idCat, idTypeTache, nomTache, descTache, ordreTache, dateDebTache, dateFinTache)
                VALUES (:idCat, :idTypeTache, :nomTache, :descTache, :ordreTache, :dateDebTache, :dateFinTache)
SQL
            );
        } else {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
            UPDATE Tache
                SET idCat=:idCat, idTypeTache=:idTypeTache, nomTache=:nomTache, descTache=:descTache, ordreTache=:ordreTache, dateDebTache=:dateDebTache, dateFinTache=:dateFinTache
                WHERE idTache = :idTache
SQL
            );
            $request->bindValue("idTache", $this->idTache);
        }

        $request->bindValue("idCat", $this->idCat);
        $request->bindValue("idTypeTache", $this->idTypeTache);
        $request->bindValue("nomTache", $this->nomTache);
        $request->bindValue("descTache", $this->descTache);
        $request->bindValue("ordreTache", $this->ordreTache);
        $request->bindValue("dateDebTache", $this->dateDebTache);
        $request->bindValue("dateFinTache", $this->dateFinTache);
        $request->execute();
    }

    public function delete(): void
    {
        if (!isset($this->idTache)) {
            throw new \Exception("Tache : delete : Impossible de supprimer une tâche inconnue");
        }

        $request = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Tache
                WHERE idTache = :idTache;
SQL
        );
        $request->bindValue("idTache", $this->idTache);
    }
}
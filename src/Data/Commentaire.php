<?php
declare(strict_types=1);

namespace data;

use InvalidArgumentException;

/**
 * Class commentaire
 */
class Commentaire implements IWritableEntity
{
    /**
     * @var int identifiant du commentaire
     */
    private int $idCom;
    /**
     * @var string contenu du commentaire
     */
    private string $contenuCom;
    /**
     * @var string date de publication du commentaire
     */
    private string $dateCom;
    /**
     * @var int id du user
     */
    private int $idUser;
    /**
     * @var int id du commentaire parent
     */
    private ?int $idComParent;
    /**
     * @var int id de la tache
     */
    private ?int $idTache;

    private array $subComments;

    private Utilisateur $utilisateur;

    public function __destruct()
    {
        $this->registerOrUpdate();
    }

    /**
     * @return Utilisateur
     */
    public function getUtilisateur(): Utilisateur
    {
        if (!isset($this->utilisateur)) {
            $this->utilisateur = Utilisateur::createFromId($this->getIdUser());
        }

        return $this->utilisateur;
    }

    /**
     * @return int
     */
    public function getIdCom(): int
    {
        return $this->idCom;
    }

    /**
     * Crée un commentaire a partir de son identifiant.
     * @param int $idUserCom identifiant du commentaire
     * @return Commentaire
     * @throws \Exception si l'id est incorrect
     */
    public static function createFromId(int $idUserCom): Commentaire
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Commentaire
    WHERE idCom = :idCom
SQL
        );
        $request->bindParam('idCom', $idUserCom);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new \Exception("Commentaire : createFromId : id inconnu");
        }

        return $request->fetch();
    }

    /**
     * Méthode permettant de récupérer tous les commentaires d'une tâche
     * @param int $idTag Identifiant de la tâche
     * @return array Tableau de commentaires
     */
    public static function fetchTasksComments(int $idTache): array
    {
        $stmt = \MyPDO::getInstance()->prepare(<<<SQL
        SELECT * 
        FROM Commentaire
        WHERE idTache = :idTache
        ORDER BY dateCom
SQL
        );

        $stmt->execute(['idTache' => $idTache]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        return $stmt->fetchAll();

    }

    public function addComment(Commentaire $commentaire): void
    {
        if (!isset($this->subComments)) {
            $this->subComments = Commentaire::fetchTasksSubComments($this->idCom);
        }

        $this->subComments[] = $commentaire;
        $commentaire->setIdComParent($this->idComParent);
    }

    public function removeComment(Commentaire $commentaire): bool
    {
        if (!isset($this->subComments)) {
            $this->subComments = Commentaire::fetchTasksSubComments($this->idCom);
        }

        $i = 0;
        while ($i < count($this->subComments) && $this->subComments[$i] != $commentaire) {
            $i++;
        }
        $res = $i < count($this->subComments);
        if ($res) {
            $this->subComments[$i]->delete();
            $this->subComments = array_splice($this->subComments, $i, 1);
        }
        return $res;
    }

    /**
     * @return int
     */
    public function getIdComParent(): int
    {
        return $this->idComParent;
    }

    /**
     * @param int $idComParent
     */
    public function setIdComParent(int $idComParent): void
    {
        $this->idComParent = $idComParent;
    }

    public function getSubComments(): array
    {
        if (!isset($this->subComments)) {
            $this->subComments = Commentaire::fetchTasksSubComments($this->idCom);
        }

        return $this->subComments;
    }

    public function getSubComment(int $i)
    {
        if (!isset($this->subComments)) {
            $this->subComments = Commentaire::fetchTasksSubComments($this->idCom);
        }

        if($i < 0 && $i >= count($this->subComments))
        {
            throw new InvalidArgumentException();
        }

        return $this->subComments[$i];
    }

    public function registerOrUpdate(): void
    {
        if (!isset($this->idCom)) {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO `Commentaire`(`idUser`, `idComParent`, `idTag`, `contenuCom`, `dateCom`) 
            VALUES (:idUser,:idComparent,:idTag,:contenuCom,:dateCom)
SQL
            );
        } else {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
                UPDATE `Commentaire` SET `idUser`=:idUser,`idComParent`=:idComParent,`idTache`=:idTache,`contenuCom`=:contenuCom,`dateCom`=:dateCom WHERE `idCom` = :idCom
SQL
            );
            $request->bindValue("idCom", $this->idCom);
        }
        $request->bindValue("idUser", $this->idUser);
        $request->bindValue("idComParent", $this->idComParent);
        $request->bindValue("idTache", $this->idTache);
        $request->bindValue("contenuCom", $this->contenuCom);
        $request->bindValue("dateCom", $this->dateCom);
        $request->execute();
    }

    /**
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    /**
     * @param int $isUser
     */
    public function setIdUser(int $isUser): void
    {
        $this->idUser = $isUser;
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

    /**
     * @return string
     */
    public function getContenuCom(): string
    {
        return $this->contenuCom;
    }

    /**
     * @param string $contenuCom
     */
    public function setContenuCom(string $contenuCom): void
    {
        $this->contenuCom = $contenuCom;
    }

    /**
     * @return string
     */
    public function getDateCom(): string
    {
        return $this->dateCom;
    }

    /**
     * @param string $dateCom
     */
    public function setDateCom(string $dateCom): void
    {
        $this->dateCom = $dateCom;
    }

    public function delete(): void
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM `Commentaire` WHERE `idCom` = :idCom
SQL
        );
        $request->bindValue(":idCom", $this->getIdComParent());
        $request->execute();
    }

    /**
     * Méthode permettant de récupérer tous les commentaires d'une tâche
     * @param int $idTag Identifiant de la tâche
     * @return array Tableau de commentaires
     */
    public static function fetchTasksSubComments(int $idCom): array
    {
        $stmt = \MyPDO::getInstance()->prepare(<<<SQL
            SELECT * 
            FROM Commentaire
            WHERE idComParent = :idCom
            ORDER BY dateCom
SQL
        );

        $stmt->execute([':idCom' => $idCom]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS, Commentaire::class);
        return $stmt->fetchAll();
    }
}// Fin de la classe Commentaire

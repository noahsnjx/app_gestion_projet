<?php
declare(strict_types=1);

namespace data;

use Exception;
use MyPDO;
use PDO;

class Etiquette implements IWritableEntity
{
    /**
     * @var int identifiant etiquette
     */
    private int $idTag;

    /**
     * @var int identifiant couleur
     */
    private int $idCouleur;

    /**
     * @var int identifiant tableau
     */
    private int $idTab;

    /**
     * @var string nom Etiquette
     */
    private string $nomTag;

    private Couleur $color;

    public static function createFromId(int $idUserTag): Etiquette
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Etiquette
    WHERE idTag = :idTag
SQL
        );
        $request->bindParam('idTag', $idUserTag);
        $request->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        if($request->rowCount() == 0)
        {
            throw new Exception("");
        }

        $tag = $request->fetch();
        $tag->color = Couleur::createFromId($tag->idCouleur);

        return $tag;
    }

    /**
     * Acceseur sur l'id d'étiquette
     * @return int
     */
    public function getIdTache(): int
    {
        return $this->idTag;
    }

    /**
     * Accesseur sur le code couleur
     * @return int
     */
    public function getIdCouleur(): int
    {
        return $this->idCouleur;
    }

    /**
     * Modification de la couleur
     * @param int $idCouleur
     */
    public function setIdCouleur(int $idCouleur): void
    {
        $this->idCouleur = $idCouleur;
    }

    /**
     * Accesseur sur l'id' du tableau
     * @return int
     */
    public function getIdTab(): int
    {
        return $this->idTab;
    }

    /**
     * Modification de l'id du tableau accueillant l'étiquette
     * @param int $idTab
     */
    public function setIdTab(int $idTab): void
    {
        $this->idTab = $idTab;
    }

    /**
     * Accesseur sur le nom de l'étiquette
     * @return string
     */
    public function getNomTag(): string
    {
        return $this->nomTag;
    }

    /**
     * Modification du nom
     * @param string $nomTag
     */
    public function setNomTag(string $nomTag): void
    {
        $this->nomTag = $nomTag;
    }

    /**
     * @return Couleur
     */
    public function getColor(): Couleur
    {
        return $this->color;
    }

    /**
     * Enregistre toutes les modifications faîtes sur la classe     */

    public function registerOrUpdate(): void
    {
        if (!isset($this->idTag)) {
            $request = MyPDO::getInstance()->prepare(<<<SQL
                    INSERT INTO `Etiquette` (`idTab`, `nomTag`, `idCouleur`) VALUES (:idTab,:nomTag,:icCouleur)
SQL
            );
        } else {
            $request = MyPDO::getInstance()->prepare(<<<SQL
                UPDATE `Etiquette` SET `idTab`=:idTab,`nomTag`=:nomTag,`idCouleur` = :idCouleur WHERE idTag= :idTag
SQL
            );
            $request->bindValue("idTag", $this->idTag);
        }
        $request->bindValue("idTab", $this->idTab);
        $request->bindValue("nomTag", $this->nomTag);
        $request->bindValue("idCouleur", $this->idCouleur);
        $request->execute();
    }

    /**
     * Méthode permettant de récupérer toutes les étiquettes d'un tableau
     * @param int $idTab Identifiant du tableau
     * @return array Tableau d'étiquettes
     */
    public static function fetchBoardTags(int $idTab): array
    {
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
            SELECT * 
            FROM Etiquette
            WHERE idTab = :idTab
            ORDER BY idTag
SQL
        );

        $stmt->execute(['idTab' => $idTab]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Etiquette::class);
        $boardTags = $stmt->fetchAll();
        return $boardTags;
    }

    /**
     * Méthode permettant de récupérer toutes les étiquettes d'une tâche
     * @param int $idTag Identifiant de la tâche
     * @return array Tableau d'étiquettes
     */
    public static function fetchTaskTags(int $idTache): array
    {
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
            SELECT * 
            FROM Etiquette
            WHERE idTag IN 
                (SELECT idTag FROM Etiquetage
                 WHERE idTache = :idTache) 
            ORDER BY idTag
SQL
        );

        $stmt->execute(['idTache' => $idTache]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Etiquette::class);
        return $stmt->fetchAll();
    }

    public function delete(): void
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM `Etiquette` WHERE `idTag` = :idTag
SQL
        );
        $request->bindValue("idTag", $this->getIdTache());
        $request->execute();
    }
} // fin de la classe Etiquette

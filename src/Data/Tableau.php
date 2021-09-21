<?php
declare(strict_types=1);

namespace data;

use Exception;
use IteratorAggregate;
use MyPDO;

/**
 * Classe Tableau, permettant de créer un tableau contenant catégories et tâches.
 */
class Tableau implements IteratorAggregate, IWritableEntity
{
    /**
     * @var int Identifiant du Tableau
     */
    private int $idTab;

    private array $categories;

    /**
     * @var string Nom du Tableau
     */
    private string $nomTab;

    /**
     * Fabrique une instance d'album à partir d'un identifiant
     * @param int $idUser Identifiant
     * @return static Tableau
     * @throws Exception Si l'id n'est pas dans la BD
     */
    public static function createFromId(int $idUser): self
    {
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
            SELECT idTab, nomTab
            FROM Tableau
            WHERE idTab = :id
SQL
        );

        $stmt->execute([':id' => $idUser]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS, Tableau::class);
        if (($tableau = $stmt->fetch()) !== false) {
            return $tableau;
        }
        throw new \Exception("Tableau : createFromId : id inconnu");
    }

    /**
     * Accesseur sur l'identifiant.
     * @return int identifiant
     */
    public function getIdTableau(): int
    {
        return $this->idTab;
    }

    /**
     * Accesseur sur le nom du Tableau.
     * @return int Nom du Tableau
     */
    public function getNomTableau(): string
    {
        return $this->nomTab;
    }

    /**
     * @param string $nomTab
     */
    public function setNomTab(string $nomTab): void
    {
        $this->nomTab = $nomTab;
    }

    /**
     * Méthode permettant de modifier les informations du tableau
     */
    public function registerOrUpdate(): void
    {
        if (!isset($this->idTab)) {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
                    INSERT INTO `Tableau`(`nomTab`) VALUES (:nomTab)
SQL
            );
        } else {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
                UPDATE `Tableau` SET `nomTab`=:nomTab WHERE idTab = :idTab
SQL
            );
            $request->bindValue(":idTab", $this->idTab);
        }
        $request->bindValue(":nomTab", $this->nomTab);
        $request->execute();
    }

    /**
     * Méthode permettant de supprimer un tableau
     */
    public function delete(): void
    {
        $stmt = \MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Tableau
            WHERE idTab = :idTab
        SQL
        );
        $stmt->bindValue('idTab', $this->idTab);
        $stmt->execute();
    }

    /**
     * Méthode permettant d'ajouter une catégorie à un tableau.
     * @param Categorie $cat Catégorie à ajouter
     * @param int $order Ordre de la catégorie
     */
    public function addCategory(Categorie $cat, int $order = -1): void
    {
        if (!isset($this->categories)) {
            $this->categories = Categorie::fetchCategories($this->idTab);
        }

        if ($order >= 0 && $order < count($this->categories)) {
            $tmp = [];
            for($i = 0; $i < count($this->categories); $i++)
            {
                if($i == $order) {
                    $tmp[] = $cat;
                    $cat->setOrdreCat($order);
                }
                $tmp[] = $this->categories[$i];
                $this->categories[$i]->setOrdreTache($i);
            }
            $this->categories = $tmp;
        } else {
            $this->categories[] = $cat;
            $cat->setOrdreCat(count($this->categories));
        }
    }

    /**
     * Méthode permettant de retirer une catégorie dans un tableau.
     */
    public function removeCategory(Categorie $cat): bool
    {
        if (!isset($this->categories)) {
            $this->categories = Categorie::fetchCategories($this->idTab);
        }

        $i = 0;
        while ($i < count($this->categories) && $cat != $this->categories[$i]) {
            $i++;
        }
        $res = $i < count($this->categories);
        $this->categories = array_splice($this->categories, $i, 1);
        return $res;
    }

    public function __destruct()
    {
        $this->registerOrUpdate();
    }

    public function getCategorie(int $i): Categorie{
        if (!isset($this->categories)) {
            $this->categories = Categorie::fetchCategories($this->idTab);
        }

        if($i < 0 && $i >= count($this->categories))
        {
            throw new InvalidArgumentException();
        }

        return $this->categories[$i];
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        if (!isset($this->categories)) {
            $this->categories = Categorie::fetchCategories($this->idTab);
        }

        return $this->categories;
    }

    /**
     * Méthode permettant de récupérer tous les tableaux d'une équipe
     * @param int $idEquipe Identifiant de l'équipe
     * @return array Tableau de tableaux
     */
    public static function fetchBoards(int $idEquipe): array
    {
        $stmt = \MyPDO::getInstance()->prepare(<<<SQL
            SELECT * 
            FROM Tableau
            WHERE idEquipe = :idEquipe
            ORDER BY idTab
SQL
        );

        $stmt->execute([':idEquipe' => $idEquipe]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS, Tableau::class);
        $boards = $stmt->fetchAll();
        return $boards;

    }

    public function getIterator()
    {
        return new \ArrayIterator($this->categories);
    }
}
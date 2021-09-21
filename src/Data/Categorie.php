<?php
declare(strict_types=1);

namespace data;

use ArrayIterator;
use Exception;
use http\Exception\InvalidArgumentException;
use IteratorAggregate;
use MyPDO;
use PDO;

/**
 * Class Categorie
 */
class Categorie implements IteratorAggregate, IWritableEntity
{
    /**
     * @var int identifient du tableau
     */
    private int $idTab;
    /**
     * @var int identifiant de la categorie
     */
    private int $idCat;
    /**
     * @var string nom de la categorie
     */
    private string $nomCat;
    /**
     * @var int ordre de la categorie
     */
    private int $ordreCat;
    /**
     * @var array list de tache
     */
    private array $tasks;

    public function __destruct()
    {
        $this->registerOrUpdate();
    }

    /**
     * Crée une categorie a partir de son identifiant.
     * @param int $idUserCat identifiant de la categorie
     * @return Categorie
     * @throws Exception si l'id est incorrect
     */
    public static function createFromId(int $idUserCat): Categorie
    {

        $request = MyPDO::getInstance()->prepare(<<<SQL
            SELECT *
            FROM Cactegorie
            WHERE idCat = :idCat
SQL
        );
        $request->bindParam('idCat', $idUserCat);
        $request->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new Exception("Categorie : createFromId : id inconnu");
        }

        return $request->fetch();

    }

    /**
     * Méthode permettant de récupérer toutes les catégories d'un tableau
     * @param int $idTab Identifiant du tableau
     * @return array Tableau de catégories
     */
    public static function fetchCategories(int $idTab): array
    {
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
            SELECT * 
            FROM Categorie
            WHERE idTab = :idTab
            ORDER BY idCat
SQL
        );
        $stmt->execute([':idTab' => $idTab]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Categorie::class);
        $categories = $stmt->fetchAll();
        return $categories;
    }

    /**
     * @return int
     */
    public function getIdCat(): int
    {
        return $this->idCat;
    }

    /**
     * @return array
     */
    public function setTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @return int
     */
    public function getIdTab(): int
    {
        return $this->idTab;
    }

    /**
     * @param int $idTab
     */
    public function setIdTab(int $idTab): void
    {
        $this->idTab = $idTab;
    }

    /**
     * @return string
     */
    public function getNomCat(): string
    {
        return $this->nomCat;
    }

    /**
     * @param string $nomCat
     */
    public function setNomCat(string $nomCat): void
    {
        $this->nomCat = $nomCat;
    }

    /**
     * @return int
     */
    public function getOrdreCat(): int
    {
        return $this->ordreCat;
    }

    /**
     * Ajoute un tache a la categorie
     * @param Tache $tache
     * @param int $order
     */
    //j'ai pas fait l'order je sais pas
    /**
     * @param int $ordreCat
     */
    public function setOrdreCat(int $ordreCat): void
    {
        $this->ordreCat = $ordreCat;
    }


    public function addTask(Tache $tache, int $order = -1)
    {
        if (!isset($this->tasks)) {
            $this->tasks = Tache::fetchTasks($this->idCat);
        }

        if ($order >= 0 && $order < count($this->tasks)) {
            $tmp = [];
            for($i = 0; $i < count($this->tasks); $i++)
            {
                if($i == $order) {
                    $tmp[] = $tache;
                    $tache->setOrdreTache($order);
                    $tache->registerOrUpdate();
                }
                $tmp[] = $this->tasks[$i];
                $this->tasks[$i]->setOrdreTache($i);
                $this->tasks[$i]->registerOrUpdate();
            }
            $this->tasks = $tmp;
        } else {
            $this->tasks[] = $tache;
            $tache->setOrdreTache(count($this->tasks));
            $tache->registerOrUpdate();
        }
    }

    /**
     * Retirer une  tache de la categorie
     * @param Tache $tache
     * @throws Exception
     */
    public function removeTask(Tache $tache): bool
    {
        if (!isset($this->tasks)) {
            $this->tasks = Tache::fetchTasks($this->idCat);
        }

        $i = 0;
        while ($i < count($this->tasks) && $this->tasks[$i] != $tache) {
            $i++;
        }
        $res = $i < count($this->tasks);
        if ($res) {
            $this->tasks = array_splice($this->tasks, $i, 1);
            for($i = 0; $i < count($this->tasks); $i++)
            {
                $this->tasks[$i]->setOrdreTache($i);
                $this->tasks[$i]->registerOrUpdate();
            }
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getTask(int $i): array
    {
        if (!isset($this->tasks)) {
            $this->tasks = Tache::fetchTasks($this->idCat);
        }

        if ($i < 0 && $i >= count($this->tasks)) {
            throw new InvalidArgumentException("");
        }
        return $this->tasks[$i];
    }

    /**
     * @return array
     */
    public function getTasks(): array
    {
        if (!isset($this->tasks)) {
            $this->tasks = Tache::fetchTasks($this->idCat);
        }

        return $this->tasks;
    }

    /**
     *
     * Changer l'ordre d'une tache dans categorie
     * @param Categorie $categorie
     * @param int $order
     * @throws Exception
     */
    public function changeTaskOrder(Tache $task, int $order): void
    {
        $tmp = [];
        for ($i = 0; $i < count($this->tasks); $i++) {
            if ($i == $order) {
                $tmp[] = $task;
                $task->setOrdreTache($i);
            } else {
                $tmp[] = $this->tasks[$i];
                $this->tasks[$i]->setOrdreTache($i);
            }
        }
        $this->tasks = $tmp;
    }

    /**
     * Supprime une categorie
     * @throws Exception
     */
    public function delete(): void
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
        DELETE FROM Categorie
        WHERE idCat = :idCat
SQL
        );
        $request->bindValue(":idCat", $this->idCat);
        $request->execute();
    }

    public function registerOrUpdate(): void
    {
        if (!isset($this->idCat)) {
            $request = MyPDO::getInstance()->prepare(<<<SQL
                    INSERT INTO Categorie(idTab, nomCat, ordreCat) VALUES (:idTab, :nomCat, :orderCat)
SQL
            );
        } else {
            $request = MyPDO::getInstance()->prepare(<<<SQL
                UPDATE Categorie SET idTab = :idTab,nomCat = :nomCat,ordreCat = :orderCat WHERE idCat = :idCat
SQL
            );
            $request->bindValue("idCat", $this->idCat);
        }
        $request->bindValue("idTab", $this->idTab);
        $request->bindValue("nomCat", $this->nomCat);
        $request->bindValue("orderCat", $this->ordreCat);
        $request->execute();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->tasks);
    }
}// Fin de la classe Categorie

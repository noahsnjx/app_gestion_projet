<?php
declare(strict_types=1);

namespace data;

/**
 * Class Type_tache
 */
class TypeTache implements IReadableEntity
{
    /**
     * @var int identification du type de tache
     */
    private int $idTypeTache;
    /**
     * @var string libellé du type de tache
     */
    private string $libTypeTache;

    /**
     * Crée un type de tache a partir de son identifiant.
     * @param int $idUserTypeTache identification du type de tache
     * @return TypeTache
     * @throws \Exception si l'id est incorrect
     */
    public static function createFromId(int $idUserTypeTache): TypeTache
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM typeTache
    WHERE idTypeTache = :idTypeTache
SQL
        );
        $request->bindParam('idTypeTache', $idUserTypeTache);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new \Exception("Tache : createFromId : id inconnu");
        }
        return $request->fetch();
    }

    /**
     * Accesseur sur l'identifiant.
     * @return int identifiant
     */
    public function getId(): int
    {
        return $this->idTypeTache;
    }

    /**
     * Accesseur sur le libellé.
     * @return int libellé
     */
    public function getLib(): string
    {
        return $this->libTypeTache;
    }
}// Fin de la classe type tache
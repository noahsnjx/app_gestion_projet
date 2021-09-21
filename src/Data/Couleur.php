<?php
declare(strict_types=1);

namespace data;

/**
 * Classe Couleur, permettant d'affecter une couleur à une étiquette.
 */
class Couleur implements IReadableEntity
{
    /**
     * @var int Identifiant de la couleur
     */
    private int $idCouleur;

    /**
     * @var string Code hexadécimal de la couleur
     */
    private string $codeCouleur;

    /**
     * Fabrique une instance de couleur à partir d'un identifiant
     * @param int $idUser Identifiant
     * @return static Couleur
     * @throws \Exception Si l'id n'est pas dans la BD
     */
    public static function createFromId(int $idUser): self
    {
        $stmt = \MyPDO::getInstance()->prepare(<<<SQL
            SELECT idCouleur, codeCouleur
            FROM Couleur 
            WHERE idCouleur = :id
SQL
        );

        $stmt->execute([':id' => $idUser]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS, Couleur::class);
        if (($couleur = $stmt->fetch()) !== false) {
            return $couleur;
        }
        throw new \Exception("Couleur : createFromId : id inconnu");

    }

    /**
     * Accesseur sur l'identifiant d'une couleur.
     * @return int Identifiant d'une couleur
     */
    public function getIdCouleur(): int
    {
        return $this->idCouleur;
    }

    /**
     * Accesseur sur le code héxadécimal d'une couleur.
     * @return string Code hexadécimal d'une couleur
     */
    public function getCodeCouleur(): string
    {
        return $this->codeCouleur;
    }
} // fin de la Classe Couleur
<?php
declare(strict_types=1);

namespace data;

use Exception;
use MyPDO;
use PDO;

/**
 * Classe Role, permettant d'affecter un rôle à un utilisateur
 */
class Role implements IReadableEntity
{
    /**
     * @var int Identifiant du rôle
     */
    private int $idRole;

    /**
     * @var string Libellé du rôle
     */
    private string $libRole;

    /**
     * Fabrique une instance de role à partir d'un identifiant
     * @param int $idUser Identifiant
     * @return static Role
     * @throws Exception Si l'id n'est pas dans la BD
     */

    public static function createFromId(int $idUser): self
    {
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
            SELECT idRole,libRole
            FROM Role
            WHERE idRole = :id
SQL
        );

        $stmt->execute([':id' => $idUser]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Role::class);
        if (($role = $stmt->fetch()) !== false) {
            return $role;
        }
        throw new Exception("Role : createFromId : id inconnu");

    }

    /**
     * Accesseur sur l'identifiant d'un rôle
     * @return int Identifiant du rôle
     */
    public function getIdRole(): int
    {
        return $this->idRole;
    }

    /**
     * Accesseur sur le libellé d'un rôle
     * @return string Libellé du rôle
     */

    public function getLibRole(): string
    {
        return $this->libRole;
    }

} // fin de la Classe Role
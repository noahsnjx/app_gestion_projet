<?php declare(strict_types=1);

namespace data;

/**
 * Class Utilisateur permettant de crée un utilisateur.
 */
class Utilisateur implements IWritableEntity
{
    /**
     * @var int identifiant de l'utilisateur
     */
    private string $idUser;
    /**
     * @var string mail de l'utilisateur
     */
    private string $mailUser;
    /**
     * @var string mot de passe de l'utilisateur
     */
    private string $mdpUser;
    /**
     * @var string pseudo de l'utilisateur
     */
    private string $pseudoUser;
    /**
     * @var string nom de l'utilisateur
     */
    private string $nomUser;
    /**
     * @var string prenom de l'ut   ilisateur
     */
    private string $pnomUser;

    public function __destruct()
    {
        $this->registerOrUpdate();
    }

    /**
     * Crée un utilisateur a partir de son identifiant.
     * @param int $idUser identifiant de l'utilisateur
     * @return Utilisateur
     * @throws \Exception si l'id est incorrect
     */
    public static function createFromId(int $idUser): Utilisateur
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Utilisateur
    WHERE idUser = :idUser
SQL
        );
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->bindParam('idUser', $idUser);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new \Exception("Utilisateur : createFromId : id inconnu");
        }

        $u = $request->fetch();
        return $u;
    }

    public function setCookie(): void
    {
        setcookie("nomUser", $this->nomUser);
        setcookie("pnomUser", $this->pnomUser);
        setcookie("emailUser", $this->mailUser);
        setcookie("pseudoUser", $this->pseudoUser);
        setcookie("idUser",(string)$this->idUser);
    }

    /**
     * Accesseur sur le nom.
     * @return string nom
     */
    public function getNom(): string
    {
        return $this->nomUser;
    }

    /**
     * Accesseur sur le prenom.
     * @return string prenom
     */
    public function getPnom(): string
    {
        return $this->pnomUser;
    }

    /**
     * Accesseur sur l'adresse mail.
     * @return string adresse mail
     */
    public function getMail(): string
    {
        return $this->mailUser;
    }

    /**
     * Accesseur sur le pseudo.
     * @return string pseudo
     */
    public function getPseudo(): string
    {
        return $this->pseudoUser;
    }

    /**
     * Accesseur sur l'identifiant.
     * @return int identifiant
     */
    public function getId(): int
    {
        return $this->idUser;
    }

    /**
     * Accesseur sur le mot de passe.
     * @return string mot de passe
     */
    public function getMdp(): string
    {
        return $this->mdpUser;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser(int $idUser): void
    {
        $this->idUser = $idUser;
    }

    /**
     * @param string $mailUser
     */
    public function setMailUser(string $mailUser): void
    {
        $this->mailUser = $mailUser;
    }

    /**
     * @param string $mdpUser
     */
    public function setMdpUser(string $mdpUser): void
    {
        $this->mdpUser = $mdpUser;
    }

    /**
     * @param string $nomUser
     */
    public function setNomUser(string $nomUser): void
    {
        $this->nomUser = $nomUser;
    }

    /**
     * @param string $pnomUser
     */
    public function setPnomUser(string $pnomUser): void
    {
        $this->pnomUser = $pnomUser;
    }

    /**
     * @param string $pseudoUser
     */
    public function setPseudoUser(string $pseudoUser): void
    {
        $this->pseudoUser = $pseudoUser;
    }

    /**
     * Méthode permettant d'ajouter l'utilisateur à une equipe.
     */
    public function joinTeam(Equipe $team): void
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
        INSERT INTO Consitiuer(idUser, idEquipe, idRole)
             VALUES (:idUser, :idEquipe, :idRole)
SQL
        );
        $request->bindValue(":idUser", $this->idUser);
        $request->bindValue(":idEquipe", $team->getIdEquipe());
        $request->bindValue(":idRole", 0);
        $request->execute();
    }

    public function leaveTeam(Equipe $team): void
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
        DELETE FROM Consitiuer
            WHERE idUser = :idUser AND idEquipe = :idEquipe
SQL
        );
        $request->bindValue(":idUser", $this->idUser);
        $request->bindValue(":idEquipe", $team->getIdEquipe());
        $request->execute();
    }

    public function registerOrUpdate(): void
    {
        if (!isset($this->idUser)) {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
            INSERT INTO Utilisateur(mailUser, mdpUser, pseudoUser, nomUser, pnomUser) 
                VALUES (:mailUser, :mdpUser, :pseudoUser, :nomUser, :pnomUser)
SQL
            );
        } else {
            $request = \MyPDO::getInstance()->prepare(<<<SQL
            UPDATE Utilisateur 
                SET mailUser=:mailUser, mdpUser=:mdpUser, pseudoUser=:pseudoUser, nomUser=:nomUser, pnomUser=:pnomUser 
                WHERE idUser = :idUser
SQL
            );
            $request->bindValue(":idUser", $this->idUser);
        }
        $request->bindValue(":mailUser", $this->mailUser);
        $request->bindValue(":mdpUser", $this->mdpUser);
        $request->bindValue(":pseudoUser", $this->pseudoUser);
        $request->bindValue(":nomUser", $this->nomUser);
        $request->bindValue(":pnomUser", $this->pnomUser);
        $request->execute();
    }

    public function delete(): void
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
        DELETE FROM Utilisateur
            WHERE idUser = :idUser
SQL
        );
        $request->bindValue(":idUser", $this->idUser);
        $request->execute();
    }

    public static function fetchMembers(int $idEquipe): array
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT Utilisateur.idUser, mailUser, mdpUser, pseudoUser, nomUser, pnomUser 
    FROM Utilisateur, Constituer
    WHERE Constituer.idUser=Utilisateur.idUser AND idEquipe = :idEquipe
SQL
        );
        $request->bindParam('idEquipe', $idEquipe);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        return $request->fetchAll();
    }

    public static function fetchInvitedMembers(int $idEquipe): array
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT Utilisateur.idUser, mailUser, mdpUser, pseudoUser, nomUser, pnomUser 
    FROM Utilisateur, Inviter
    WHERE Inviter.idUser=Utilisateur.idUser AND idEquipe = :idEquipe
SQL
        );
        $request->bindParam('idEquipe', $idEquipe);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        return $request->fetchAll();
    }

    public static function fetchAssignees(int $idTache): array
    {
        $request = \MyPDO::getInstance()->prepare(<<<SQL
    SELECT Utilisateur.idUser, mailUser, mdpUser, pseudoUser, nomUser, pnomUser 
    FROM Affecter, Utilisateur
    WHERE Affecter.idUser=Utilisateur.idUser AND idTache = :idTache
SQL
        );
        $request->bindParam('idTache', $idTache);
        $request->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        return $request->fetchAll();
    }

}// Fin de la classe Utilisateur

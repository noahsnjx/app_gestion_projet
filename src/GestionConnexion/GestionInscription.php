<?php
declare(strict_types=1);

class GestionInscription
{


    /**
     * @return bool
     * @throws Exception
     */
    public static function verif_user(string $pseudo): bool
    {
        $result = true;
        $stmt_pseudo = MyPDO::getInstance()->prepare("SELECT idUser FROM Utilisateur WHERE pseudoUser=?");
        $stmt_pseudo->setFetchMode(PDO::FETCH_NUM);
        $stmt_pseudo->execute([$pseudo]);
        if (($stmt_pseudo->rowCount()) > 0)
            $result = false;
        return $result;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public static function verif_mail(string $mail): bool
    {
        $result = true;
        $stmt_email = MyPDO::getInstance()->prepare("SELECT idUser FROM Utilisateur WHERE mailUser=?");
        $stmt_email->setFetchMode(PDO::FETCH_NUM);
        $stmt_email->execute([$mail]);
        var_dump($stmt_email->rowCount());
        if (($stmt_email->rowCount()) > 0)
            $result = false;
        return $result;
    }

    /**
     * @throws Exception
     */
    public static function insert_profil(string $mail, string $mdp, string $pseudo, string $nom, string $prnm): \data\Utilisateur
    {

        $stmt = MyPDO::getInstance()->prepare(<<< SQL
INSERT INTO Utilisateur (mailUser,mdpUser,pseudoUser, nomUser, pnomUser)
VALUES (:mail,:mdp,:pseudo, :nom, :prnm)
SQL
        );
        $stmt->bindValue(":mail", $mail);
        $stmt->bindValue(":mdp", $mdp);
        $stmt->bindValue(":pseudo", $pseudo);
        $stmt->bindValue(":nom", $nom);
        $stmt->bindValue(":prnm", $prnm);
        $stmt->execute();

        $requete = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Utilisateur
    WHERE mailUser = :mail

SQL
        );

        $requete->bindValue(':mail', $mail);
        $requete->setFetchMode(PDO::FETCH_CLASS, \data\Utilisateur::class);
        $requete->execute();
        return $requete->fetch();

    }

    /**
     * @return bool
     */
    public static function verif_chaine(string $mdp, string $conf_mdp): bool
    {
        return ($mdp == $conf_mdp);
    }


}
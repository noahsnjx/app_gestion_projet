<?php
declare(strict_types=1);

class GestionConnexion
{
    private function __construct(){

    }

    public static function isConnexionAccepted(string $email, string $password): \data\Utilisateur{
        $requete = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Utilisateur
    WHERE mailUser = :mail
    AND mdpUser = :password

SQL);

        $requete->bindValue('mail', $email);
        $requete->bindValue('password', $password);
        $requete->setFetchMode(PDO::FETCH_CLASS,\data\Utilisateur::class);
        $requete->execute();
        var_dump($requete->rowCount());
        if (($requete->rowCount()) >0) {
            return $requete->fetch();
        }
        throw new InvalidArgumentException('Utilisateur introuvé');
    }

}
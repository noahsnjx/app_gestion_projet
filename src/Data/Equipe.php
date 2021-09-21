<?php
declare(strict_types=1);
namespace data;

use Exception;
use InvalidArgumentException;
use MyPDO;
use PDO;

class Equipe implements IWritableEntity
{
    /**
     * @var int identifiant équipe
     */
    private int $idEquipe;

    /**
     * @var string nom de l'équipe
     */
    private string $nomEquipe;

    /**
     * @var array tableau de tableaux
     */
    private array $boards;

    /**
     * @var array membre de l'equipe
     */
    private array $members;

    /**
     * @var array invite un Membres
     */
    private array $invitedMembers;

    public function __destruct()
    {
        $this->registerOrUpdate();
    }

    public static function createFromId(int $idUserEquipe): Equipe
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Equipe
    WHERE idEquipe = :idEquipe
SQL
        );
        $request->bindParam('idEquipe',  $idUserEquipe);
        $request->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new Exception("Equipe : createFromId : id inconnu");
        }

        return $request->fetch();
    }

    /**
     * @return int
     */
    public function getIdEquipe(): int
    {
        return $this->idEquipe;
    }

    /**
     * @return string
     */
    public function getNomEquipe(): string
    {
        return $this->nomEquipe;
    }
    
    /**
     * @param string $nomEquipe
     */
    public function setNomEquipe(string $nomEquipe): void
    {
        $this->nomEquipe = $nomEquipe;
    }

    public function addBoard(Tableau $board): void
    {
        if (!isset($this->boards)) {
            $this->boards = Tableau::fetchBoards($this->idEquipe);
        }

        $this->boards[]=$board;
    }

    public function removeBoard(Tableau $board): bool
    {
        if (!isset($this->boards)) {
            $this->boards = Tableau::fetchBoards($this->idEquipe);
        }

        $result=false;
        $order=0;
        while($order<count($this->boards) && $this->boards[$order]!=$board){
            if($this->boards[$order]==$board){
                $this->boards[$order]=[];
                $result=true;
            }
            $order+=1;
        }
        return $result;
    }

    public function getBoard(int $i): Tableau
    {
        if (!isset($this->boards)) {
            $this->boards = Tableau::fetchBoards($this->idEquipe);
        }

        if($i < 0 && $i >= count($this->boards))
        {
            throw new InvalidArgumentException();
        }

        return $this->boards[$i];
    }

    public function inviteSomeone(Utilisateur $user): void
    {
        if (!isset($this->invitedMembers)) {
            $this->invitedMembers = Utilisateur::fetchInvitedMembers($this->idEquipe);
        }

        $request = MyPDO::getInstance()->prepare(<<<SQL
        INSERT INTO Inviter(idEquipe, dateInvit)
            VALUES (:idEquipe, :dateInvit)
SQL);
        $request->bindValue(":idEquipe",$this->idEquipe);
        $request->bindValue(":dateInvit",date("Y-m-d",time()));
        $request->execute();

        $this->invitedMembers[]=$user;
    }

    public function registerOrUpdate(): void
    {
        if(!isset($this->idEquipe)){
            $request = MyPDO::getInstance()->prepare(<<<SQL
                    INSERT INTO `Equipe`(`nomEquipe`) VALUES (:nomEquipe)
SQL);
            $request->bindValue(":nomEquipe",$this->nomEquipe);
            $request->execute();
        }else{
            $request = MyPDO::getInstance()->prepare(<<<SQL
                   UPDATE `Equipe` SET `nomEquipe`=:nomEquipe WHERE `idEquipe` = :idEquipe
SQL);
            $request->bindValue(":nomEquipe",$this->nomEquipe);
            $request->bindValue(":idEquipe",$this->idEquipe);
            $request->execute();
        }
    }

    public function delete(): void
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
        DELETE FROM Equipe
        WHERE idEquipe = :idEquipe
SQL
        );
        $request->bindValue(":idEquipe",$this->idEquipe);
        $request->execute();
    }

    public static function fetchTeams(int $idUser): array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Constituer, Equipe
    WHERE Constituer.idEquipe=Equipe.idEquipe AND idUser = :idUser
SQL
        );
        $request->bindParam('idUser',  $idUser);
        $request->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $request->execute();

        return $request->fetchAll();
    }

}



<?php
require_once "autoload.php";

$webpage= new WebPage("Profil");
$webpage->appendToHead(<<<HTML
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Questrial">
HTML
);
$pastille= new ProfilComponent((int)$_COOKIE['idUser'],$_COOKIE['pnomUser']);
$webpage->appendContent(<<<HTML
<div class="background d-flex flex-column">
    <header class="sticky-top menubar">
        <div class="d-inline d-sm-flex justify-content-between">
            <div class="d-flex align-items-center fw-bold flex-grow-1">
                <i class="mx-1 bi bi-chevron-left"></i>
                <div class="mx-2">LOGO</div>
                <div class="mx-2 vl"></div>
                <div class="mx-2">Nom</div>
            </div>
            <div class="d-flex flex-row align-items-center justify-content-between">
                {$pastille->toHTML()}
                <div class="button bg-white mx-3 my-1 p-1 shadow">
                    Disconnect
                </div>
            </div>
        </div>
    </header>
    <div class="m-3 d-flex flex-column align-items-stretch flex-grow-1 justify-content-evenly">
        <div class="text-white d-flex m-2 p-2 flex-column panel">
            <h1>Profil</h1>
            <div class="d-flex flex-column align-items-center">
                <div class="profile text-white font-large bg-primary m-2">{$pastille->toHTML()}</div>
                <form name="Modification" method="post" action="Profil.php">
                <div class="d-flex flex-column flex-lg-row justify-content-center align-items-stretch w-100">
                    <div class="m-2 flex-fill">
                        Informations personnelles
                        <input class="form-control m-1" type="text" id="pseudo" name="pseudo" value="{$_COOKIE['pseudoUser']}" placeholder="Pseudo" required>
                        <input class="form-control m-1" type="text" id="NOMprenom" name="NOMprenom" value="{$_COOKIE['nomUser']} {$_COOKIE['pnomUser']}" placeholder="NOM Prénom" required>
                    </div>

                    <div class="m-2 flex-fill">
                        Adresse e-mail
                        <input class="form-control m-1" type="text" id="eMail" name="eMail" value="{$_COOKIE['emailUser']}" placeholder="e-mail" required>
                        <input class="form-control m-1" type="text" id="Mail" name="Mail" placeholder="Vérification e-mail" required>
                    </div>
                    <div class="m-2 flex-fill">
                        Mot de passe
                        <input class="form-control m-1" type="password" id="mdp" name="mdp" placeholder="Mot de passe" required>
                        <input class="form-control m-1" type="password" id="mdp2" name="mdp2" placeholder="Vérification mot de passe" required>
                    </div>
                </div>
                <div class="m-2">
                    <button class="button bg-gray px-4 " type="submit">Modifier</button>
                </div>
                </form>
            </div>
        </div>
        <div class="panel text-white d-flex flex-column m-2 p-2">
            <h1>Mes Tableaux</h1>
            <div class="d-flex m-2 scrollable-x align-items-stretch">
                <div
                    class="bg-tableau flex-column text-white minh-150 maxw-200 border-medium minw-200 text-center d-flex justify-content-center align-items-center m-2">
                    Tableau 1
                    <div class="d-flex">
                        <i class="btn btn-danger bottom-0 bi bi-trash text-white m-1 shadow"></i>
                        <i class="btn btn-primary bottom-0 bi bi-eye-fill text-white m-1 shadow"></i>
                    </div>
                </div>
                <div class="text-black bg-white flex-column minh-150 maxw-200 border-medium minw-200 text-center d-flex justify-content-center align-items-center m-2"> + 
                </div>
            </div>
        </div>
        <div class="panel d-flex text-white flex-column m-2 p-2">
            <h1>Mes équipes</h1>
            <div class="d-flex m-2 flex-grow-1 align-items-stretch scrollable-x">
                <div class="minh-100 border-medium minw-200 bg-white m-2 p-2 ">
                    <div
                        class="flex-wrap d-flex justify-content-center align-items-center">
                        <div class="profile text-white bg-primary m-1 fs-5">M</div>
                        <div class="profile text-white bg-primary m-1 fs-5">M</div>
                        <div class="profile text-white bg-secondary m-1 fs-5">+2</div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <i class="btn btn-danger bottom-0 bi bi-trash text-white m-1 shadow"></i>
                        <i class="btn btn-primary bottom-0 bi bi-eye-fill text-white m-1 shadow"></i>
                    </div>
                </div>
                <div
                    class="border-medium minw-200 d-flex bg-white m-2 text-dark justify-content-center align-items-center">
                    +
                </div>
            </div>
        </div>
    </div>
</div>

HTML
);

try{
    $idUser=(int)$_COOKIE['idUser'];
    $user=\data\Utilisateur::createFromId($idUser);
    if (!(isset($_POST['eMail']) && isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['mdp2']) && isset($_POST['Mail']) && isset($_POST['NOMprenom']))) {
        throw new InvalidArgumentException('Pas def');
    }
    if (!(GestionInscription::verif_chaine($_POST['mdp'], $_POST['mdp2'] ))) {
        throw new InvalidArgumentException('Les mots de passe ne sont pas identiques');
    }
    if (!(GestionInscription::verif_chaine($_POST['eMail'], $_POST['Mail'] ))) {
        throw new InvalidArgumentException('Les e-mails ne sont pas identiques');
    }
    if ($user->getMail()!= $_POST['Mail']){
        if (!(GestionInscription::verif_mail($_POST['eMail']))) {
            throw new InvalidArgumentException('Mail déjà utilisé');
    }}
    if ($user->getPseudo()!=$_POST['pseudo']){
        if (!(GestionInscription::verif_user($_POST['pseudo']))) {
            throw new InvalidArgumentException('pseudo déjà utilisé');

    }}
    $user->setMailUser($_POST['eMail']);
    $user->setMdpUser($_POST['mdp']);
    $user->setPseudoUser($_POST['pseudo']);
    $user->registerOrUpdate();
    $user->setCookie();
    header("Location:Profil.php", true, 302);

}catch (Exception $e){
}

echo $webpage->toHTML();
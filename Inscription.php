<?php
declare(strict_types=1);
require_once "autoload.php";

$webpage = new WebPage("Inscription");
$webpage->appendToHead(<<< HTML
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
HTML
);
$webpage->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css");
$webpage->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css");
$webpage->appendCssUrl("style/style.css");
$webpage->appendCssUrl("https://fonts.googleapis.com/css?family=Questrial");

try {
    $webpage->appendContent(<<<HTML
<div class="background d-flex flex-column h-100">
    <header class="sticky-top menubar">
        <div class="d-inline d-sm-flex justify-content-between">
            <div class="d-flex align-items-center fw-bold flex-grow-1">
                <div class="mx-3">LOGO</div>
                <div class="mx-2 vl"></div>
                <div class="mx-2">Inscription</div>
            </div>
        </div>
    </header>

    <div class="overflow-hidden d-flex flex-grow-1 ">
        <div class="slide-in-fade d-flex justify-content-center align-content-center flex-grow-1">
            <div class="form-box bg-white border-large ">
                <form name="Inscription" class="d-flex flex-column align-items-stretch" method="post" action="Inscription.php">
                    <h1 class="text-center">Inscription</h1>
                    <div class="d-flex">
                        <div class="m-2 flex-fill">
                            <label for="nom">
                                Nom:
                            </label>
                            <input class="form-control" placeholder="Nom" type="text" id="nom" name="nom" required>
                        </div>
                        <div class="m-2 flex-fill">
                            <label for="prnm">
                                Prénom:
                            </label>
                            <input class="form-control" placeholder="Prénom" type="text" id="prnm" name="prnm" required>
                        </div>
                    </div>
                    <div class="m-2">
                        <label for="email">
                            E-mail:
                        </label>
                        <input class="form-control" placeholder="E-mail" type="email" id="email" name="email" required>
                    </div>
                    <div class="m-2">
                        <label for="pseudo">
                            Pseudo:
                        </label>
                        <input class="form-control" placeholder="Pseudonyme" type="text" id="pseudo" name="pseudo" required>
                    </div>
                    <div class="m-2">
                        <label for="password">
                            Mot de passe:
                        </label>
                        <input class="form-control" placeholder="Mot de passe" type="password" id="password" name="password" required>
                    </div>
                    <div class="m-2">
                        <label for="confirm_password">
                            Répéter mot de passe:
                        </label>
                        <input class="form-control" placeholder="Mot de passe" type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="m-2 text-center">
                        <button class="button bg-gray fs-4 py-2 px-4" type="submit" >Inscription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
HTML
    );

    if (!(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['pseudo']) && !empty($_POST['pseudo']) && isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['confirm_password']) && !empty($_POST['confirm_password']) && isset($_POST['nom']) && !empty($_POST['nom']) && isset($_POST['prnm']) && !empty($_POST['prnm']))) {
        throw new InvalidArgumentException('Pas def');
    }
    if (!(GestionInscription::verif_chaine($_POST['password'], $_POST['confirm_password'] ))) {
        throw new InvalidArgumentException('Les mots de passe sont identiques');
    }
    if (!(GestionInscription::verif_mail($_POST['email']))) {
        throw new InvalidArgumentException('Mail déjà utilisé');
    }
    if (!(GestionInscription::verif_user($_POST['pseudo']))) {
        throw new InvalidArgumentException('pseudo déjà utilisé');
    }
    $user = GestionInscription::insert_profil($_POST['email'],$_POST['password'],$_POST['pseudo'],$_POST['nom'],$_POST['prnm']);
    $user->setCookie();
    header("Location:Profil.php ", true, 302);
} catch (Exception $e) {
}
echo $webpage->toHTML();

<?php
declare(strict_types=1);
require_once "autoload.php";

$maPage = new WebPage("Connexion");
$maPage->appendToHead(<<< HTML
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
HTML
);
$maPage->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css");
$maPage->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css");
$maPage->appendCssUrl("style/style.css");
$maPage->appendCssUrl("https://fonts.googleapis.com/css?family=Questrial");


$maPage->appendContent(<<<HTML
    <div class="background d-flex flex-column h-100">
        <header class="sticky-top menubar">
            <div class="d-inline d-sm-flex justify-content-between">
                <div class="d-flex align-items-center fw-bold flex-grow-1">
                    <div class="mx-3">LOGO</div>
                    <div class="mx-2 vl"></div>
                    <div class="mx-2">Connexion</div>
                </div>
            </div>
        </header>
    
        <div class="overflow-hidden d-flex flex-grow-1 ">
            <div class="slide-in-fade d-flex justify-content-center align-content-center flex-grow-1">
                <div class="form-box bg-white border-large">
                    <form name="Connexion" method="post" action="Connexion.php">
                        <div class="p-2">
                            <h1 class="text-center">Connexion</h1>
                            <div class="m-2">
                                <label for="email">E-mail</label>
                                <input class="form-control" type="email" id="email" name="email" required
                                    placeholder="E-mail">
                            </div>
                            <div class="m-2">
                                <label for="password">Mot de passe</label>
                                <input class="form-control" type="password" id="password" name="password" required
                                    placeholder="Mot de passe">
                            </div>
                        </div>
                        <div class="m-2 text-center">
                            <button class="button bg-gray font-medium py-2 px-4" type="submit">Connexion</button>
                            <p>
                                <a class="link"
                                    href="inscription.php">Créer
                                    un compte</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

HTML
);
try {
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        throw new InvalidArgumentException("email vide");
    }

    if (!isset($_POST['password']) || empty($_POST['password'])) {
        throw new InvalidArgumentException("Mot de passe vide");
    }

    if (($user = GestionConnexion::isConnexionAccepted($_POST['email'], $_POST['password'])) !== false) {
        $user->setCookie();
        header("Location:Profil.php ", true, 302);
    }
}
catch (Exception $e) {
}
echo $maPage->toHTML();


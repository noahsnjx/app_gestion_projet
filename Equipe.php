<?php
declare(strict_types=1);
require_once "../../autoload.php";

$equipe = new WebPage("Equipe");

$equipe->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css");
$equipe->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css","sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x");
$equipe->appendCssUrl("../style/style.css");
$equipe->appendCssUrl("https://fonts.googleapis.com/css?family=Questrial");

if(isset($_GET['idUser']) && !empty($_GET['idUser']))
{
    $teams=\data\Equipe::fetchTeams($_GET['idUser']);
    $numTeam=0;
    $member=\data\Utilisateur::fetchMembers($teams[$numTeam]->idEquipe());
    $user=\data\Utilisateur::createFromId($_GET['idUser']);
    $profil=ProfilComponent::__construct($user->getId(), $user->getPnom());
}

$equipe->appendContent(<<<HTML
<div class="d-flex flex-column background">
    <header class="sticky-top menubar">
		<div class="d-inline scrollable-x d-sm-flex justify-content-between">
			<div class="d-flex align-items-center fw-bold flex-grow-1">
				<i class="mx-1 bi bi-chevron-left"></i>
				<div class="mx-2">LOGO</div>
				<div class="mx-2 vl"></div>
				<div class="mx-2">{$teams[$numTeam]->getNomEquipe()}</div>
				<i class="bi bi-pencil-fill"></i>
			</div>
			<div class="d-flex flex-row align-items-center justify-content-between">
				<div class="button bg-white mx-3 my-1 p-1 shadow">
					<a class="link"
					   href="Connextion.php">Disconnect</a>
				</div>
HTML);
$equipe->appendContent("{$profil->toHTML()}");
$equipe->appendContent(<<<HTML
			</div>
		</div>
	</header>

	<div class="m-3 d-flex flex-column align-items-stretch flex-grow-1 justify-content-evenly">
		<div class="d-flex text-white flex-column panel m-2 p-2">
			<h1>Membres</h1>
			<div class="font-large scrollable-x d-flex flex-grow-1 flex-wrap">
HTML);
$member=\data\Utilisateur::fetchMembers($teams[$numTeam]->idEquipe());
foreach ($member as $numMember){
    $otherUser=\data\Utilisateur::createFromId($numMember->getId());
    $profil=ProfilComponent::__construct($otherUser->getId(),$otherUser->getPnom());
    $equipe->appendContent("{$profil->toHTML()}");
}
$equipe->appendContent(<<<HTML
			</div>
		</div>

		<div class="panel d-flex text-white flex-column m-2 p-2">
			<h1>Tableaux de l'équipe</h1>
			<div class="d-flex m-2 scrollable-x align-items-stretch">
				<div class="bg-tableau minh-150 maxw-200 border-medium minw-200 d-flex text-center justify-content-center align-items-center m-2">
HTML);
$tableau=\data\Tableau::fetchBoards($teams[$numTeam]->getIdEquipe());
foreach ($tableau as $numTableau){
    $otherTab=\data\Tableau::createFromId($numTableau->getIdEquipe());
    $equipe->appendContent("{$otherTab->getNomTableau()}");
}
$equipe->appendContent(<<<HTML
				</div>
				<div class="border-medium minw-200 d-flex bg-white shadow m-2 text-dark justify-content-center align-items-center">+</div>
			</div>
		</div>

		<div class="panel text-white m-2 p-2">
			<h1>Inviter un membre dans votre équipe</h1>
			<form>
				<div class="d-flex flex-column flex-grow-1">
					<label for="mail_invite" class="m-2 ">Adresse-mail du membre à inviter :</label>
					<div class="d-flex flex-grow-1 align-items-center">
						<input class="form-control" type="text" id="mail_invite" name="mail_invite" placeholder="e-mail de l'utilisateur à inviter">
						<button class="button m-2 d-flex" type="submit">Inviter<i class="ms-2 bi bi-person-plus-fill"></i></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
HTML);

echo $equipe->toHTML();

<?php
declare(strict_types=1);
require_once "autoload.php";

use \data\Tableau;

$maPage = new WebPage("Tableau");
$maPage->appendToHead(<<< HTML
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
HTML
);
$maPage->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css");
$maPage->appendCssUrl("https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css");
$maPage->appendCssUrl("style/style.css");
$maPage->appendCssUrl("https://fonts.googleapis.com/css?family=Questrial");
$maPage->appendJsUrl("https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js");
$maPage->appendJsUrl("https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js");
$maPage->appendJs(<<<JS
$(document).ready(function(){
    $('.task-modifier').click(function(){
        var task_id = $(this).data('taskid');
            // AJAX request
            $.ajax({
                url: 'ModalTask.php',
                type: 'post',
                data: {task_id: task_id},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-content').html(response);
                    
                    // Display Modal
                    $('#taskModal').modal('show'); 
            }
        });
    });
    
    $('.comments-show').click(function(){
        var task_id = $(this).data('taskid');
            // AJAX request
            $.ajax({
                url: 'ModalCommentaire.php',
                type: 'post',
                data: {task_id: task_id},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-content').html(response);
                    
                    // Display Modal
                    $('#taskModal').modal('show'); 
            }
        });
    });
});

JS);

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception();
    }
    $tableau = Tableau::createFromId((int)$_GET['id']);

    $html = <<<HTML
    <body class="background d-flex flex-column">
        <header class="sticky-top menubar">
            <div class="d-inline d-sm-flex justify-content-between">
                <div class="d-flex align-items-center fw-bold flex-grow-1">
                    <i class="mx-1 bi bi-chevron-left"></i>
                    <div class="mx-2">LOGO</div>
                    <div class="mx-2 vl"></div>
                    <div contenteditable="true" class="single-line mx-2">{$tableau->getNomTableau()}</div>
                    <i class="bi bi-pencil-fill"></i>
                </div>
                <div class="d-flex flex-row align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="profile text-white bg-primary mx-1">M</div>
                        <div class="profile text-white bg-danger mx-1">M</div>
                        <div class="profile text-white bg-success mx-1">M</div>
                    </div>
                    <div class="button mx-3 bg-white my-1 p-1 shadow">
                        Manage
                    </div>
                </div>
            </div>
        </header>
    
        <div class="scrollable-x d-flex flex-column flex-md-row align-content-start">
HTML;

    $tc = new TableauComponent($tableau);
    $html .= $tc->toHTML();

    $html .= <<<HTML
        </div>
        <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </body>
HTML;

    $maPage->appendContent($html);
} catch (Exception $e) {
    echo $e->getMessage();
}
echo $maPage->toHTML();


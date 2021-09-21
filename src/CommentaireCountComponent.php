<?php


use data\Commentaire;
use data\Tache;

class CommentaireCountComponent extends Component
{
    private Tache $tache;
    private int $count;

    public function __construct(Tache $tache)
    {
        parent::__construct();
        $this->tache = $tache;
        $this->count = 0;
        foreach ($tache->getComments() as $comment) {
            $this->count++;
            foreach ($comment->getSubComments() as $subComment) {
                $this->count++;
            }
        }
    }

    public function toHTML(): string
    {
        return <<<HTML
                <button type="button" class="d-flex btn btn-secondary btn-sm comments-show" data-taskid="{$this->tache->getidTache()}">
                    <i class="bi bi-chat-left-dots-fill text-white mx-1"></i>
                    {$this->count}
                </button>

HTML;

    }
}
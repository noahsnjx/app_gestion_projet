<?php
require_once "Comment.php";

$com=new Comment(1);
echo $com->toHtml(1);
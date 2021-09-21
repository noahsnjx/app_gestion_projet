<?php


abstract class Component{

    protected array $components;

    public function __construct(Component... $component){
        $this->components = $component;

    }

    public function addComponent(Component $component){
        $this->components[] = $component;
    }

    protected function componentsToHTML(): string{
        $html = "";
        foreach ($this->components as $component){
            $html .= $component->toHTML();
        }

        return $html;
    }

    public abstract function toHTML(): string;
}
<?php

namespace App\Entity;

use DateTime;

class Article
{
    private $id;
    private $title;
    private $content;
    private $date;
    private $dateObject = null;
    private $slug = null;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getDate()
    {
        if (!$this->dateObject && $this->date) {
            $this->dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
        }
        return $this->dateObject;
    }

    public function getSlug()
    {
        if (!$this->slug) {
            // Generate a slug from the title if not set
            $this->slug = strtolower(str_replace(' ', '-', $this->title));
        }
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setDate($date)
    {
        $this->dateObject = $date;
        return $this;
    }
}

<?php


namespace Server;


class Message
{

    private $content;

    private $id;

    /**
     * Message constructor.
     * @param $content
     * @param $id
     */
    public function __construct($content, $id)
    {
        $this->content = $content;
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getMessageOutput()
    {
        return "User {$this->id}: {$this->content}";
    }
}
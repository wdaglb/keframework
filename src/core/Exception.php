<?php
namespace ke;

class Exception extends \Exception
{

    protected $data = [];

    protected function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

}

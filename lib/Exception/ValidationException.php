<?php

namespace Z38\IbanTool\Exception;

class ValidationException extends \RuntimeException implements ExceptionInterface
{
    protected $flag;

    public function __construct($flag)
    {
        parent::__construct(sprintf('Could not convert to IBAN (validation flag: %d)', $flag));

        $this->flag = $flag;
    }

    public function getFlag()
    {
        return $this->flag;
    }
}

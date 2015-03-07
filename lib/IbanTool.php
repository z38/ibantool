<?php

namespace Z38\IbanTool;

use Z38\IbanTool\Exception\RuntimeException;
use Z38\IbanTool\Exception\ValidationException;

/**
 * IbanTool
 */
class IbanTool
{
    protected $options;
    protected $process;
    protected $processInput;
    protected $processOutput;

    /**
     * Constructor
     *
     * @param array $options Configuration options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge(array(
            'java' => '/usr/bin/java',
            'ibantool_jar' => __DIR__.'/../java/ibantool_java.jar',
            'bridge_jar' => __DIR__.'/../java/bridge.jar'
        ), $options);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->stopProcess();
    }

    /**
     * Convert a PC account number to IBAN
     *
     * @param string $account Postal account number
     *
     * @return string IBAN
     *
     * @throws ValidationException When the account number is invalid
     * @throws RuntimeException    When an error occurs during processing
     */
    public function convertPostal($account)
    {
        return $this->convert('POFICHBEXXX', $account);
    }

    /**
     * Convert a tuple of an account number and a PC/BC/BIC to IBAN
     *
     * @param string $bcpc          PC/BC/BIC of the institution
     * @param string $accountNumber Account number
     *
     * @return string IBAN
     *
     * @throws ValidationException When the account number is invalid
     * @throws RuntimeException    When an error occurs during processing
     */
    public function convert($bcpc, $accountNumber)
    {
        $this->startProcess();

        fwrite($this->processInput, sprintf("%s;%s\n", $this->sanitizeInput($bcpc), $this->sanitizeInput($accountNumber)));

        $result = fgets($this->processOutput);
        if ($result === false) {
            throw new RuntimeException('Could not fetch output from IBAN-Tool.');
        }

        $resultParts = explode(';', trim($result));
        if (count($resultParts) != 2) {
            throw new RuntimeException('Could not parse output from IBAN-Tool.');
        }

        $validationFlag = intval(ltrim($resultParts[0], '0'));
        if ($validationFlag <= 0) {
            throw new RuntimeException(sprintf('Unknown validation flag %d.', $validationFlag));
        }
        if ($validationFlag >= 10) {
            throw new ValidationException($validationFlag);
        }

        return $resultParts[1];
    }

    protected function startProcess()
    {
        if ($this->process !== null) {
            $status = proc_get_status($this->process);
            if ($status !== false && $status['running']) {
                return;
            }
        }

        $this->process = proc_open($this->buildCommand(), array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w')
        ), $pipes);
        $this->processInput = $pipes[0];
        $this->processOutput = $pipes[1];
    }

    protected function stopProcess()
    {
        if ($this->process !== null) {
            fclose($this->processInput);
            fclose($this->processOutput);
            proc_close($this->process);
            $this->process = null;
        }
    }

    protected function buildCommand()
    {
        $arguments = array(
            $this->options['java'],
            '-cp',
            implode(':', array($this->options['ibantool_jar'], $this->options['bridge_jar'])),
            'z38.ibantool.Bridge',
            '2>&1'
        );

        return implode(' ', array_map('escapeshellarg', $arguments));
    }

    protected function sanitizeInput($input)
    {
        return preg_replace('/[^A-Za-z0-9. -]/', '', $input);
    }
}

<?php

namespace Thiagoprz\CpfValidator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;


/**
 * @package \Thiagoprz\Cpf
 */
class Cpf extends Validator
{
    /**
     * @param Validation $validation
     * @param string $attribute
     * @return bool
     */
    public function validate(Validation $validation, $attribute)
    {
        // Extracting only numbers since the value can be using a mask
        $cpf = preg_replace( '/[^0-9]/is', '', $validation->getValue($attribute));

        if (   strlen($cpf) != 11 // Check size number
            || preg_match('/(\d)\1{10}/', $cpf) // Avoiding repeated digits. (111.111.111-11)
            || !$this->validateMod11($cpf) // Validating based on the mod11 calculation of the CPF
        ) {
            $message = $this->getOption('message');

            if (!$message) {
                $message = 'The CPF is not valid';
            }

            $validation->appendMessage(new Message($message, $attribute, 'Cpf'));
            return false;
        }

        return true;
    }

    /**
     * @param string $cpf
     * @return boolean
     */
    private function validateMod11($cpf)
    {
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}
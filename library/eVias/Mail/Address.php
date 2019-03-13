<?php
/**
 *
 * Made by
 * Login   <osirven@elma.fr>
 *
 * Started on  Wed Jan  3 15:55:29 2007
 * Last update Wed Jan  3 18:14:33 2007
 *
 *
 * $Id: Address.php 1166 2009-06-17 17:58:50Z fabien $
 * @author $Author: fabien $
 * @version $Revision: 1166 $
 * @copyright Elma Ingénierie Informatique
 * @filesource
 * @package
 */


class eVias_Mail_Address 
	extends ArrayObject
{
    const ALLOW_INVALID_MX      = true;
    const DISALLOW_INVALID_MX   = false;

    public function __construct($email, $name = '', $isInvalidMxAllowed = self::DISALLOW_INVALID_MX)
    {
        $this->setEmail($email, $isInvalidMxAllowed);
        $this->setName($name);
    }

    /**
     * Sets the email value
     *
     * @param $email string the value to set
     * @throw Elma_Mail_Exception
     */
    public function setEmail($email, $isInvalidMxAllowed = self::DISALLOW_INVALID_MX)
    {
          $this->offsetSet('email', $email);
    }

    /**
     * Get the email address
     *
     * @return string
     */
    public function getEmail()
    {
        return ($this->offsetGet('email'));
    }

    /**
     * Sets the name value
     *
     * @param $name string the value to set
     */
    public function setName($name)
    {
        $this->offsetSet('name', $name);
    }

    /**
     * Get the first name value
     *
     * @return string
     */
    public function getName()
    {
        return ($this->offsetGet('name'));
    }

    /**
     * Read only property accessor
     *
     * @param $key string property to fetch
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {
        case 'email':
            return ($this->getEmail());
            break;
        case 'name':
            return ($this->getName());
        default:
            return (false);
        }
    }

    /**
     * Returns true if the first name or the last name are not empty
     *
     * @return boolean
     */
    public function hasName()
    {
        $str = $this->getName();

        return (!empty($str));
    }

    /**
     * Convert the object into a string representation
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->hasName()) {
            return ($this->email);
        }

        return ($this->name . ' <' . $this->email . '>');
    }

    /**
     * Construct a new eVias_Mail_Address from a string
     *
     * @param   $str                string  a string containing the email and name
     * @param   $isInvalidMxAllowed boolean Whether e-mail addresses with invalid MX will be rejected or not
     *
     * @return  eVias_Mail_Address
     * @throw   eVias_Mail_Exception
     */
    public static function factory($str, $isInvalidMxAllowed = self::DISALLOW_INVALID_MX)
    {
        preg_match('/^(.*<)?(.*)(>.*)?$/', trim($str), $matches);
        $cnt = count($matches);

        if ($cnt < 2) {
            throw new eVias_Mail_Exception('invalid string: \'' . $str . '\'');
        }

        $email = trim(str_replace('>', '', $matches[2]));
        $name = trim(str_replace('<', '', $matches[1]));

        return new self($email, $name, $isInvalidMxAllowed);
    }
}

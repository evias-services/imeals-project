<?php

/**
 * @file Mail.php
 * @ingroup ElmaZendExtensionFramework
 * @brief Elma_Mail code
 *
 * Code of class Elma_Mail
 */


/**
 * @brief Concrete class for handling view scripts.
 *
 * @ingroup ElmaZendExtensionFramework
 *
 * $Author: llevesque $
 * $Date: 2006-05-26 11:17:03 +0200 (ven, 26 mai 2006) $
 * $Id: Mail.php 52 2006-05-26 09:17:03Z llevesque $
 * $Revision: 52 $
 */
class eVias_Mail 
	extends Zend_Mail
{
    /**
     * Public constructor
     *
     * @param string $charset
     */
    public function __construct($charset = 'utf-8')
    {
        parent::__construct($charset);
        $tr = new Zend_Mail_Transport_Smtp('localhost');
        self::setDefaultTransport($tr);

		$this->setFrom('noreply@simple.gsaive.japanim.fr');
    }

    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * If ELMA_MAIL_TEST all mail recipients are removed and set to ELMA_MAIL_TEST value.
     *
     * @param  Zend_Mail_Transport_Abstract $transport
     * @return Zend_Mail                    Provides fluent interface
     */
    public function send($transport = null)
    {
        return parent::send($transport);
    }

    /**
     * Destructor to cleanly disconnect from server.
     *
     */
    public function __destruct()
    {
        if (method_exists(self::$_defaultTransport, '__destruct')) {
            self::$_defaultTransport->__destruct();
        }
    }

    /**
     * Parse adresses for : from, reply-to, return-path, to, cc and bcc
     *
     * @param array $adresses
     *
     * @return Elma_Mail
     */
    public function parseAddresses(array $adresses)
    {
        foreach ($adresses as $header => $recipients) {
            foreach ($recipients as $recipient) {
                if ('' == trim($recipient)) {
                    continue;
                }
                $email = Elma_Mail_Address::factory($recipient);

                if (!$email->hasName()) {
                    $email->setName($email->getEmail());
                }

                switch ($header) {
                    case 'from':
                        $this->setFrom($email->getEmail(), $email->getName());
                        break;
                    case 'reply-to':
                        $this->addHeader('Reply-To', $email->getEmail());
                        break;
                    case 'return-path':
                        try {
                            $this->setReturnPath($email->getEmail());
                        } catch (Exception $e) {
                            // do nothing
                        }
                        break;
                    case 'bcc':
                        $this->addBcc($email->getEmail());
                        break;
                    case 'to':
                    case 'cc':
                        $method = 'add' . ucfirst($header);
                        $this->$method($email->getEmail(), $email->getName());
                        break;
                    default:
                        throw new Elma_Mail_Exception('invalid header: \'' . $header . '\'');
                }
            }
        }

        return $this;
    }

    protected function _encodeHeader($value)
    {
      if (Zend_Mime::isPrintable($value)) {
          return $value;
      } else {
          $quotedValue = Zend_Mime::encodeQuotedPrintable($value, 200);
          $quotedValue = str_replace(array('?', ' '), array('=3F', '=20'), $quotedValue);
          return '=?' . $this->_charset . '?Q?' . $quotedValue . '?=';
      }
    }

    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }
}


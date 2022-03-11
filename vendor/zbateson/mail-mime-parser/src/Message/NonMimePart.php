<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Message;

/**
 * Represents part of a non-mime message.
 *
 * @author Zaahid Bateson
 */
abstract class NonMimePart extends MessagePart
{
    /**
     * Returns true.
     * 
     * @return bool
     */
    public function isTextPart()
    {
        return true;
    }

    /**
     * Returns text/plain
     * 
     * @return string
     */
    public function getContentType($default = 'text/plain')
    {
        return $default;
    }

    /**
     * Returns ISO-8859-1
     * 
     * @return string
     */
    public function getCharset()
    {
        return 'ISO-8859-1';
    }

    /**
     * Returns 'inline'.
     * 
     * @return string
     */
    public function getContentDisposition($default = 'inline')
    {
        return 'inline';
    }

    /**
     * Returns '7bit'.
     * 
     * @return string
     */
    public function getContentTransferEncoding($default = '7bit')
    {
        return '7bit';
    }

    /**
     * Returns false.
     * 
     * @return bool
     */
    public function isMime()
    {
        return false;
    }

    /**
     * Returns the Content ID of the part.
     *
     * NonMimeParts do not have a Content ID, and so this simply returns null.
     *
     * @return string|null
     */
    public function getContentId()
    {
        return null;
    }
}

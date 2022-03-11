<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Message;

use ZBateson\MailMimeParser\MailMimeParser;
use ZBateson\MailMimeParser\Message\PartStreamContainer;

/**
 * Implementation of a non-mime message's uuencoded attachment part.
 *
 * @author Zaahid Bateson
 */
class UUEncodedPart extends NonMimePart implements IUUEncodedPart
{
    /**
     * @var int the unix file permission
     */
    protected $mode = null;

    /**
     * @var string the name of the file in the uuencoding 'header'.
     */
    protected $filename = null;

    public function __construct($mode = null, $filename = null, IMimePart $parent = null, PartStreamContainer $streamContainer = null)
    {
        if ($streamContainer === null) {
            $di = MailMimeParser::getDependencyContainer();
            $streamContainer = $di['ZBateson\MailMimeParser\Message\PartStreamContainer'];
            $streamFactory = $di['ZBateson\MailMimeParser\Stream\StreamFactory'];
            $streamContainer->setStream($streamFactory->newMessagePartStream($this));
        }
        parent::__construct(
            $streamContainer,
            $parent
        );
        $this->mode = $mode;
        $this->filename = $filename;
    }

    /**
     * Returns the filename included in the uuencoded 'begin' line for this
     * part.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        $this->notify();
    }

    /**
     * Returns false.
     * 
     * Although the part may be plain text, there is no reliable way of
     * determining its type since uuencoded 'begin' lines only include a file
     * name and no mime type.  The file name's extension may be a hint.
     * 
     * @return bool
     */
    public function isTextPart()
    {
        return false;
    }

    /**
     * Returns 'application/octet-stream'.
     * 
     * @return string
     */
    public function getContentType($default = 'application/octet-stream')
    {
        return 'application/octet-stream';
    }

    /**
     * Returns null
     * 
     * @return string
     */
    public function getCharset()
    {
        return null;
    }

    /**
     * Returns 'attachment'.
     * 
     * @return string
     */
    public function getContentDisposition($default = 'attachment')
    {
        return 'attachment';
    }

    /**
     * Returns 'x-uuencode'.
     * 
     * @return string
     */
    public function getContentTransferEncoding($default = 'x-uuencode')
    {
        return 'x-uuencode';
    }

    public function getUnixFileMode()
    {
        return $this->mode;
    }

    public function setUnixFileMode($mode)
    {
        $this->mode = $mode;
        $this->notify();
    }
}

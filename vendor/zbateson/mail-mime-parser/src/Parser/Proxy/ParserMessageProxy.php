<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Parser\Proxy;

/**
 * A bi-directional parser-to-part proxy for IMessage objects created by
 * MimeParser.
 *
 * @author Zaahid Bateson
 */
class ParserMessageProxy extends ParserMimePartProxy
{
    /**
     * @var int maintains the character length of the last line separator,
     *      typically 2 for CRLF, to keep track of the correct 'end' position
     *      for a part because the CRLF before a boundary is considered part of
     *      the boundary.
     */
    protected $lastLineEndingLength = 0;

    public function getLastLineEndingLength()
    {
        return $this->lastLineEndingLength;
    }

    public function setLastLineEndingLength($lastLineEndingLength)
    {
        $this->lastLineEndingLength = $lastLineEndingLength;
    }
}

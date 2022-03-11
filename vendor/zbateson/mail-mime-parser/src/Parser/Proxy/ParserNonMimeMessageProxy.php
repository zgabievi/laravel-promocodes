<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Parser\Proxy;

/**
 * A bi-directional parser-to-part proxy for IMessage objects created by
 * NonMimeParser.
 *
 * @author Zaahid Bateson
 */
class ParserNonMimeMessageProxy extends ParserMessageProxy
{
    /**
     * @var int|null The next part's start position within the message's raw
     *      stream, or null if not set, not discovered, or there are no more
     *      parts.
     */
    protected $nextPartStart = null;

    /**
     * @var int The next part's unix file mode in a uu-encoded 'begin' line if
     *      exists, or null otherwise.
     */
    protected $nextPartMode = null;

    /**
     * @var string The next part's file name in a uu-encoded 'begin' line if
     *      exists, or null otherwise.
     */
    protected $nextPartFilename = null;

    /**
     * Returns the next part's start position within the message's raw stream,
     * or null if not set, not discovered, or there are no more parts under this
     * message.
     *
     * @return int|null The start position or null
     */
    public function getNextPartStart()
    {
        return $this->nextPartStart;
    }

    /**
     * Returns the next part's unix file mode in a uu-encoded 'begin' line if
     * one exists, or null otherwise.
     *
     * @return int|null The file mode or null
     */
    public function getNextPartMode()
    {
        return $this->nextPartMode;
    }

    /**
     * Returns the next part's filename in a uu-encoded 'begin' line if one
     * exists, or null otherwise.
     *
     * @return int|null The file name or null
     */
    public function getNextPartFilename()
    {
        return $this->nextPartFilename;
    }

    /**
     * Sets the next part's start position within the message's raw stream.
     *
     * @param int $nextPartStart
     */
    public function setNextPartStart($nextPartStart)
    {
        $this->nextPartStart = $nextPartStart;
    }

    /**
     * Sets the next part's unix file mode from its 'begin' line.
     *
     * @param int $nextPartMode
     */
    public function setNextPartMode($nextPartMode)
    {
        $this->nextPartMode = $nextPartMode;
    }

    /**
     * Sets the next part's filename from its 'begin' line.
     *
     * @param string $nextPartFilename
     */
    public function setNextPartFilename($nextPartFilename)
    {
        $this->nextPartFilename = $nextPartFilename;
    }

    /**
     * Sets the next part start position, file mode, and filename to null
     */
    public function clearNextPart()
    {
        $this->nextPartStart = null;
        $this->nextPartMode = null;
        $this->nextPartFilename = null;
    }
}

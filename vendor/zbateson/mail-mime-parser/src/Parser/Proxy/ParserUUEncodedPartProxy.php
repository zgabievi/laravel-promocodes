<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Parser\Proxy;

/**
 * A bi-directional parser-to-part proxy for NonMimeParser and IUUEncodedParts.
 *
 * @author Zaahid Bateson
 */
class ParserUUEncodedPartProxy extends ParserPartProxy
{
    /**
     * Returns the next part's start position within the message's raw stream,
     * or null if not set, not discovered, or there are no more parts under this
     * message.
     *
     * As this is a message-wide setting, ParserUUEncodedPartProxy calls
     * getNextPartStart() on its parent (a ParserNonMimeMessageProxy, which
     * stores/returns this information).
     *
     * @return int|null The start position or null
     */
    public function getNextPartStart()
    {
        return $this->getParent()->getNextPartStart();
    }

    /**
     * Returns the next part's unix file mode in a uu-encoded 'begin' line if
     * one exists, or null otherwise.
     *
     * As this is a message-wide setting, ParserUUEncodedPartProxy calls
     * getNextPartMode() on its parent (a ParserNonMimeMessageProxy, which
     * stores/returns this information).
     *
     * @return int|null The file mode or null
     */
    public function getNextPartMode()
    {
        return $this->getParent()->getNextPartMode();
    }

    /**
     * Returns the next part's filename in a uu-encoded 'begin' line if one
     * exists, or null otherwise.
     *
     * As this is a message-wide setting, ParserUUEncodedPartProxy calls
     * getNextPartFilename() on its parent (a ParserNonMimeMessageProxy, which
     * stores/returns this information).
     *
     * @return int|null The file name or null
     */
    public function getNextPartFilename()
    {
        return $this->getParent()->getNextPartFilename();
    }

    /**
     * Sets the next part's start position within the message's raw stream.
     *
     * As this is a message-wide setting, ParserUUEncodedPartProxy calls
     * setNextPartStart() on its parent (a ParserNonMimeMessageProxy, which
     * stores/returns this information).
     *
     * @param int $nextPartStart
     */
    public function setNextPartStart($nextPartStart)
    {
        $this->getParent()->setNextPartStart($nextPartStart);
    }

    /**
     * Sets the next part's unix file mode from its 'begin' line.
     *
     * As this is a message-wide setting, ParserUUEncodedPartProxy calls
     * setNextPartMode() on its parent (a ParserNonMimeMessageProxy, which
     * stores/returns this information).
     *
     * @param int $nextPartMode
     */
    public function setNextPartMode($nextPartMode)
    {
        $this->getParent()->setNextPartMode($nextPartMode);
    }

    /**
     * Sets the next part's filename from its 'begin' line.
     *
     * As this is a message-wide setting, ParserUUEncodedPartProxy calls
     * setNextPartFilename() on its parent (a ParserNonMimeMessageProxy, which
     * stores/returns this information).
     *
     * @param string $nextPartFilename
     */
    public function setNextPartFilename($nextPartFilename)
    {
        $this->getParent()->setNextPartFilename($nextPartFilename);
    }

    /**
     * Returns the file mode included in the uuencoded 'begin' line for this
     * part.
     *
     * @return int
     */
    public function getUnixFileMode()
    {
        return $this->getHeaderContainer()->getUnixFileMode();
    }

    /**
     * Returns the filename included in the uuencoded 'begin' line for this
     * part.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->getHeaderContainer()->getFilename();
    }
}

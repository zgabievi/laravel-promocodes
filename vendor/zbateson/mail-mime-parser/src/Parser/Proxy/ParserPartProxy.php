<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Parser\Proxy;

use ZBateson\MailMimeParser\Message\IMessagePart;
use ZBateson\MailMimeParser\Parser\IParser;
use ZBateson\MailMimeParser\Parser\PartBuilder;

/**
 * Proxy between a MessagePart and a Parser.
 *
 * ParserPartProxy objects are responsible for ferrying requests from message
 * parts to a proxy as they're requested, and for maintaining state information
 * for a parser as necessary.
 *
 * @author Zaahid Bateson
 */
abstract class ParserPartProxy extends PartBuilder
{
    /**
     * @var IMessagePart The part.
     */
    private $part;

    /**
     * @var IParser The parser.
     */
    protected $parser;

    /**
     * @var PartBuilder The part's PartBuilder.
     */
    protected $partBuilder;

    public function __construct(
        PartBuilder $partBuilder,
        IParser $parser
    ) {
        $this->partBuilder = $partBuilder;
        $this->parser = $parser;
    }

    /**
     * Sets the associated part.
     *
     * @param IMessagePart $part The part
     */
    public function setPart(IMessagePart $part)
    {
        $this->part = $part;
    }

    /**
     * Returns the IMessagePart associated with this proxy.
     *
     * @return IMessagePart the part.
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * Requests the parser to parse this part's content, and call
     * setStreamContentStartPos/EndPos to setup this part's boundaries within
     * the main message's raw stream.
     *
     * The method first checks to see if the content has already been parsed,
     * and is safe to call multiple times.
     */
    public function parseContent()
    {
        if (!$this->isContentParsed()) {
            $this->parser->parseContent($this);
        }
    }

    /**
     * Parses everything under this part.
     *
     * For ParserPartProxy, this is just content, but sub-classes may override
     * this to parse all children as well for example.
.     */
    public function parseAll()
    {
        $this->parseContent();
    }

    public function getParent()
    {
        return $this->partBuilder->getParent();
    }

    public function getHeaderContainer()
    {
        return $this->partBuilder->getHeaderContainer();
    }

    public function getStream()
    {
        return $this->partBuilder->getStream();
    }

    public function getMessageResourceHandle()
    {
        return $this->partBuilder->getMessageResourceHandle();
    }

    public function getMessageResourceHandlePos()
    {
        return $this->partBuilder->getMessageResourceHandlePos();
    }

    public function getStreamPartStartPos()
    {
        return $this->partBuilder->getStreamPartStartPos();
    }

    public function getStreamPartLength()
    {
        return $this->partBuilder->getStreamPartLength();
    }

    public function getStreamContentStartPos()
    {
        return $this->partBuilder->getStreamContentStartPos();
    }

    public function getStreamContentLength()
    {
        return $this->partBuilder->getStreamContentLength();
    }

    public function setStreamPartStartPos($streamPartStartPos)
    {
        $this->partBuilder->setStreamPartStartPos($streamPartStartPos);
    }

    public function setStreamPartEndPos($streamPartEndPos)
    {
        $this->partBuilder->setStreamPartEndPos($streamPartEndPos);
    }

    public function setStreamContentStartPos($streamContentStartPos)
    {
        $this->partBuilder->setStreamContentStartPos($streamContentStartPos);
    }

    public function setStreamPartAndContentEndPos($streamContentEndPos)
    {
        $this->partBuilder->setStreamPartAndContentEndPos($streamContentEndPos);
    }

    public function isContentParsed()
    {
        return $this->partBuilder->isContentParsed();
    }

    public function isMime()
    {
        return $this->partBuilder->isMime();
    }
}

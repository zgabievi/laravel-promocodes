<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Message;

use ZBateson\MailMimeParser\MailMimeParser;
use GuzzleHttp\Psr7\StreamWrapper;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use SplObjectStorage;
use SplObserver;

/**
 * Most basic representation of a single part of an email.
 *
 * @author Zaahid Bateson
 */
abstract class MessagePart implements IMessagePart
{
    /**
     * @var IMimePart parent part
     */
    protected $parent;

    /**
     * @var PartStreamContainer holds 'stream' and 'content stream'.
     */
    protected $partStreamContainer;

    /**
     * @var string can be used to set an override for content's charset in cases
     *      where a user knows the charset on the content is not what it claims
     *      to be.
     */
    protected $charsetOverride;

    /**
     * @var bool set to true when a user attaches a stream manually, it's
     *      assumed to already be decoded or to have relevant transfer encoding
     *      decorators attached already.
     */
    protected $ignoreTransferEncoding;

    /**
     * @var SplObjectStorage attached observers that need to be notified of
     *      modifications to this part.
     */
    protected $observers;

    public function __construct(PartStreamContainer $streamContainer, IMimePart $parent = null)
    {
        $this->partStreamContainer = $streamContainer;
        $this->parent = $parent;
        $this->observers = new SplObjectStorage();
    }

    #[\ReturnTypeWillChange]
    public function attach(SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    #[\ReturnTypeWillChange]
    public function detach(SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    #[\ReturnTypeWillChange]
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
        if ($this->parent !== null) {
            $this->parent->notify();
        }
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function hasContent()
    {
        return $this->partStreamContainer->hasContent();
    }

    public function getFilename()
    {
        return null;
    }

    public function setCharsetOverride($charsetOverride, $onlyIfNoCharset = false)
    {
        if (!$onlyIfNoCharset || $this->getCharset() === null) {
            $this->charsetOverride = $charsetOverride;
        }
    }

    public function getContentStream($charset = MailMimeParser::DEFAULT_CHARSET)
    {
        if ($this->hasContent()) {
            $tr = ($this->ignoreTransferEncoding) ? '' : $this->getContentTransferEncoding();
            $ch = ($this->charsetOverride !== null) ? $this->charsetOverride : $this->getCharset();
            return $this->partStreamContainer->getContentStream(
                $tr,
                $ch,
                $charset
            );
        }
        return null;
    }

    public function getBinaryContentStream()
    {
        if ($this->hasContent()) {
            $tr = ($this->ignoreTransferEncoding) ? '' : $this->getContentTransferEncoding();
            return $this->partStreamContainer->getBinaryContentStream($tr);
        }
        return null;
    }

    public function getBinaryContentResourceHandle()
    {
        $stream = $this->getBinaryContentStream();
        if ($stream !== null) {
            return StreamWrapper::getResource($stream);
        }
        return null;
    }

    public function saveContent($filenameResourceOrStream)
    {
        $resourceOrStream = $filenameResourceOrStream;
        if (is_string($filenameResourceOrStream)) {
            $resourceOrStream = fopen($filenameResourceOrStream, 'w+');
        }

        $stream = Utils::streamFor($resourceOrStream);
        Utils::copyToStream($this->getBinaryContentStream(), $stream);

        if (!is_string($filenameResourceOrStream)
            && !($filenameResourceOrStream instanceof StreamInterface)) {
            // only detach if it wasn't a string or StreamInterface, so the
            // fopen call can be properly closed if it was
            $stream->detach();
        }
    }

    public function getContent($charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $stream = $this->getContentStream($charset);
        if ($stream !== null) {
            return $stream->getContents();
        }
        return null;
    }

    public function attachContentStream(StreamInterface $stream, $streamCharset = MailMimeParser::DEFAULT_CHARSET)
    {
        $ch = ($this->charsetOverride !== null) ? $this->charsetOverride : $this->getCharset();
        if ($ch !== null && $streamCharset !== $ch) {
            $this->charsetOverride = $streamCharset;
        }
        $this->ignoreTransferEncoding = true;
        $this->partStreamContainer->setContentStream($stream);
        $this->notify();
    }

    public function detachContentStream()
    {
        $this->partStreamContainer->setContentStream(null);
        $this->notify();
    }

    public function setContent($resource, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $stream = Utils::streamFor($resource);
        $this->attachContentStream($stream, $charset);
        // this->notify() called in attachContentStream
    }

    public function getResourceHandle()
    {
        return StreamWrapper::getResource($this->getStream());
    }

    public function getStream()
    {
        return $this->partStreamContainer->getStream();
    }

    public function save($filenameResourceOrStream, $filemode = 'w+')
    {
        $resourceOrStream = $filenameResourceOrStream;
        if (is_string($filenameResourceOrStream)) {
            $resourceOrStream = fopen($filenameResourceOrStream, $filemode);
        }

        $partStream = $this->getStream();
        $partStream->rewind();
        $stream = Utils::streamFor($resourceOrStream);
        Utils::copyToStream($partStream, $stream);

        if (!is_string($filenameResourceOrStream)
            && !($filenameResourceOrStream instanceof StreamInterface)) {
            // only detach if it wasn't a string or StreamInterface, so the
            // fopen call can be properly closed if it was
            $stream->detach();
        }
    }

    public function __toString()
    {
        return $this->getStream()->getContents();
    }
}

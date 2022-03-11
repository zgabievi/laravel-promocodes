<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Stream;

use Psr\Http\Message\StreamInterface;
use ZBateson\StreamDecorators\Base64Stream;
use ZBateson\StreamDecorators\CharsetStream;
use ZBateson\StreamDecorators\ChunkSplitStream;
use ZBateson\StreamDecorators\SeekingLimitStream;
use ZBateson\StreamDecorators\NonClosingStream;
use ZBateson\StreamDecorators\PregReplaceFilterStream;
use ZBateson\StreamDecorators\QuotedPrintableStream;
use ZBateson\StreamDecorators\UUStream;
use ZBateson\MailMimeParser\Message\IMessagePart;
use ZBateson\MailMimeParser\Parser\PartBuilder;

/**
 * Factory class for Psr7 stream decorators used in MailMimeParser.
 *
 * @author Zaahid Bateson
 */
class StreamFactory
{
    /**
     * Returns a SeekingLimitStream using $part->getStreamPartLength() and
     * $part->getStreamPartStartPos()
     *
     * @param PartBuilder $part
     * @return SeekingLimitStream
     */
    public function getLimitedPartStream(PartBuilder $part)
    {
        return $this->newLimitStream(
            $part->getStream(),
            $part->getStreamPartLength(),
            $part->getStreamPartStartPos()
        );
    }

    /**
     * Returns a SeekingLimitStream using $part->getStreamContentLength() and
     * $part->getStreamContentStartPos()
     *
     * @param PartBuilder $part
     * @return SeekingLimitStream
     */
    public function getLimitedContentStream(PartBuilder $part)
    {
        $length = $part->getStreamContentLength();
        if ($length !== 0) {
            return $this->newLimitStream(
                $part->getStream(),
                $part->getStreamContentLength(),
                $part->getStreamContentStartPos()
            );
        }
        return null;
    }

    /**
     * Creates and returns a SeekingLimitedStream.
     *
     * @param StreamInterface $stream
     * @param int $length
     * @param int $start
     * @return SeekingLimitStream
     */
    private function newLimitStream(StreamInterface $stream, $length, $start)
    {
        return new SeekingLimitStream(
            $this->newNonClosingStream($stream),
            $length,
            $start
        );
    }

    /**
     * Creates a non-closing stream that doesn't close it's internal stream when
     * closing/detaching.
     * 
     * @param StreamInterface $stream
     * @return NonClosingStream
     */
    public function newNonClosingStream(StreamInterface $stream)
    {
        return new NonClosingStream($stream);
    }

    /**
     * Creates a ChunkSplitStream.
     * 
     * @param StreamInterface $stream
     * @return ChunkSplitStream
     */
    public function newChunkSplitStream(StreamInterface $stream)
    {
        return new ChunkSplitStream($stream);
    }

    /**
     * Creates and returns a Base64Stream with an internal
     * PregReplaceFilterStream that filters out non-base64 characters.
     * 
     * @param StreamInterface $stream
     * @return Base64Stream
     */
    public function newBase64Stream(StreamInterface $stream)
    {
        return new Base64Stream(
            new PregReplaceFilterStream($stream, '/[^a-zA-Z0-9\/\+=]/', '')
        );
    }

    /**
     * Creates and returns a QuotedPrintableStream.
     *
     * @param StreamInterface $stream
     * @return QuotedPrintableStream
     */
    public function newQuotedPrintableStream(StreamInterface $stream)
    {
        return new QuotedPrintableStream($stream);
    }

    /**
     * Creates and returns a UUStream
     *
     * @param StreamInterface $stream
     * @return UUStream
     */
    public function newUUStream(StreamInterface $stream)
    {
        return new UUStream($stream);
    }

    /**
     * Creates and returns a CharsetStream
     *
     * @param StreamInterface $stream
     * @param string $fromCharset
     * @param string $toCharset
     * @return CharsetStream
     */
    public function newCharsetStream(StreamInterface $stream, $fromCharset, $toCharset)
    {
        return new CharsetStream($stream, $fromCharset, $toCharset);
    }

    /**
     * Creates and returns a MessagePartStream
     *
     * @param IMessagePart $part
     * @return MessagePartStream
     */
    public function newMessagePartStream(IMessagePart $part)
    {
        return new MessagePartStream($this, $part);
    }

    /**
     * Creates and returns a HeaderStream
     *
     * @param IMessagePart $part
     * @return HeaderStream
     */
    public function newHeaderStream(IMessagePart $part)
    {
        return new HeaderStream($part);
    }
}

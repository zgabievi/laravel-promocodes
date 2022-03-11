<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser;

use ZBateson\MailMimeParser\Header\HeaderConsts;
use ZBateson\MailMimeParser\Message\PartHeaderContainer;
use ZBateson\MailMimeParser\Message\MimePart;
use ZBateson\MailMimeParser\Message\PartChildrenContainer;
use ZBateson\MailMimeParser\Message\PartFilter;
use ZBateson\MailMimeParser\Message\PartStreamContainer;
use ZBateson\MailMimeParser\Message\Helper\MultipartHelper;
use ZBateson\MailMimeParser\Message\Helper\PrivacyHelper;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7;

/**
 * An email message.
 *
 * The message could represent a simple text email, a multipart message with
 * children, or a non-mime message containing UUEncoded parts.
 *
 * @author Zaahid Bateson
 */
class Message extends MimePart implements IMessage
{
    /**
     * @var MultipartHelper service providing functions for multipart messages.
     */
    private $multipartHelper;

    /**
     * @var PrivacyHelper service providing functions for multipart/signed
     *      messages.
     */
    private $privacyHelper;

    public function __construct(
        PartStreamContainer $streamContainer = null,
        PartHeaderContainer $headerContainer = null,
        PartChildrenContainer $partChildrenContainer = null,
        MultipartHelper $multipartHelper = null,
        PrivacyHelper $privacyHelper = null
    ) {
        parent::__construct(
            null,
            $streamContainer,
            $headerContainer,
            $partChildrenContainer
        );
        if ($multipartHelper === null || $privacyHelper === null) {
            $di = MailMimeParser::getDependencyContainer();
            $multipartHelper = $di['ZBateson\MailMimeParser\Message\Helper\MultipartHelper'];
            $privacyHelper = $di['ZBateson\MailMimeParser\Message\Helper\PrivacyHelper'];
        }
        $this->multipartHelper = $multipartHelper;
        $this->privacyHelper = $privacyHelper;
    }

    /**
     * Convenience method to parse a handle or string into an IMessage without
     * requiring including MailMimeParser, instantiating it, and calling parse.
     *
     * If the passed $resource is a resource handle or StreamInterface, the
     * resource must remain open while the returned IMessage object exists.
     * Pass true as the second argument to have the resource attached to the
     * IMessage and closed for you when it's destroyed, or pass false to
     * manually close it if it should remain open after the IMessage object is
     * destroyed.
     *
     * @param resource|StreamInterface|string $resource The resource handle to
     *        the input stream of the mime message, or a string containing a
     *        mime message.
     * @param bool $attached pass true to have it attached to the returned
     *        IMessage and destroyed with it.
     * @return IMessage
     */
    public static function from($resource, $attached)
    {
        static $mmp = null;
        if ($mmp === null) {
            $mmp = new MailMimeParser();
        }
        return $mmp->parse($resource, $attached);
    }

    /**
     * Returns true if the current part is a mime part.
     *
     * The message is considered 'mime' if it has either a Content-Type or
     * MIME-Version header defined.
     *
     * @return bool
     */
    public function isMime()
    {
        $contentType = $this->getHeaderValue(HeaderConsts::CONTENT_TYPE);
        $mimeVersion = $this->getHeaderValue(HeaderConsts::MIME_VERSION);
        return ($contentType !== null || $mimeVersion !== null);
    }

    public function getTextPart($index = 0)
    {
        return $this->getPart(
            $index,
            PartFilter::fromInlineContentType('text/plain')
        );
    }

    public function getTextPartCount()
    {
        return $this->getPartCount(
            PartFilter::fromInlineContentType('text/plain')
        );
    }

    public function getHtmlPart($index = 0)
    {
        return $this->getPart(
            $index,
            PartFilter::fromInlineContentType('text/html')
        );
    }

    public function getHtmlPartCount()
    {
        return $this->getPartCount(
            PartFilter::fromInlineContentType('text/html')
        );
    }

    public function getTextStream($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $textPart = $this->getTextPart($index);
        if ($textPart !== null) {
            return $textPart->getContentStream($charset);
        }
        return null;
    }

    public function getTextContent($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $part = $this->getTextPart($index);
        if ($part !== null) {
            return $part->getContent($charset);
        }
        return null;
    }

    public function getHtmlStream($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $htmlPart = $this->getHtmlPart($index);
        if ($htmlPart !== null) {
            return $htmlPart->getContentStream($charset);
        }
        return null;
    }

    public function getHtmlContent($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $part = $this->getHtmlPart($index);
        if ($part !== null) {
            return $part->getContent($charset);
        }
        return null;
    }

    public function setTextPart($resource, $charset = 'UTF-8')
    {
        $this->multipartHelper
            ->setContentPartForMimeType(
                $this, 'text/plain', $resource, $charset
            );
    }

    public function setHtmlPart($resource, $charset = 'UTF-8')
    {
        $this->multipartHelper
            ->setContentPartForMimeType(
                $this, 'text/html', $resource, $charset
            );
    }

    public function removeTextPart($index = 0)
    {
        return $this->multipartHelper
            ->removePartByMimeType(
                $this, 'text/plain', $index
            );
    }

    public function removeAllTextParts($moveRelatedPartsBelowMessage = true)
    {
        return $this->multipartHelper
            ->removeAllContentPartsByMimeType(
                $this, 'text/plain', $moveRelatedPartsBelowMessage
            );
    }

    public function removeHtmlPart($index = 0)
    {
        return $this->multipartHelper
            ->removePartByMimeType(
                $this, 'text/html', $index
            );
    }

    public function removeAllHtmlParts($moveRelatedPartsBelowMessage = true)
    {
        return $this->multipartHelper
            ->removeAllContentPartsByMimeType(
                $this, 'text/html', $moveRelatedPartsBelowMessage
            );
    }

    public function getAttachmentPart($index)
    {
        return $this->getPart(
            $index,
            PartFilter::fromAttachmentFilter()
        );
    }

    public function getAllAttachmentParts()
    {
        return $this->getAllParts(
            PartFilter::fromAttachmentFilter()
        );
    }

    public function getAttachmentCount()
    {
        return count($this->getAllAttachmentParts());
    }

    public function addAttachmentPart($resource, $mimeType, $filename = null, $disposition = 'attachment', $encoding = 'base64')
    {
        $this->multipartHelper
            ->createAndAddPartForAttachment(
                $this,
                $resource,
                $mimeType,
                (strcasecmp($disposition, 'inline') === 0) ? 'inline' : 'attachment',
                $filename,
                $encoding
            );
    }

    public function addAttachmentPartFromFile($filePath, $mimeType, $filename = null, $disposition = 'attachment', $encoding = 'base64')
    {
        $handle = Psr7\Utils::streamFor(fopen($filePath, 'r'));
        if ($filename === null) {
            $filename = basename($filePath);
        }
        $this->addAttachmentPart($handle, $mimeType, $filename, $disposition, $encoding);
    }

    public function removeAttachmentPart($index)
    {
        $part = $this->getAttachmentPart($index);
        $this->removePart($part);
    }

    public function getSignedMessageStream()
    {
        return $this
            ->privacyHelper
            ->getSignedMessageStream($this);
    }

    public function getSignedMessageAsString()
    {
        return $this
            ->privacyHelper
            ->getSignedMessageAsString($this);
    }

    public function getSignaturePart()
    {
        if (strcasecmp($this->getContentType(), 'multipart/signed') === 0) {
            return $this->getChild(1);
        } else {
            return null;
        }
    }

    public function setAsMultipartSigned($micalg, $protocol)
    {
        $this->privacyHelper
            ->setMessageAsMultipartSigned($this, $micalg, $protocol);
    }

    public function setSignature($body)
    {
        $this->privacyHelper
            ->setSignature($this, $body);
    }
}

<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Message\Helper;

use ZBateson\MailMimeParser\IMessage;
use ZBateson\MailMimeParser\Header\HeaderConsts;
use ZBateson\MailMimeParser\Message\Factory\IMimePartFactory;
use ZBateson\MailMimeParser\Message\Factory\IUUEncodedPartFactory;
use ZBateson\MailMimeParser\Message\IMessagePart;

/**
 * Provides routines to set or retrieve the signature part of a signed message.
 *
 * @author Zaahid Bateson
 */
class PrivacyHelper extends AbstractHelper
{
    /**
     * @var GenericHelper a GenericHelper instance
     */
    private $genericHelper;

    /**
     * @var MultipartHelper a MultipartHelper instance
     */
    private $multipartHelper;

    public function __construct(
        IMimePartFactory $mimePartFactory,
        IUUEncodedPartFactory $uuEncodedPartFactory,
        GenericHelper $genericHelper,
        MultipartHelper $multipartHelper
    ) {
        parent::__construct($mimePartFactory, $uuEncodedPartFactory);
        $this->genericHelper = $genericHelper;
        $this->multipartHelper = $multipartHelper;
    }

    /**
     * The passed message is set as multipart/signed, and a new part is created
     * below it with content headers, content and children copied from the
     * message.
     *
     * @param IMessage $message
     * @param string $micalg
     * @param string $protocol
     */
    public function setMessageAsMultipartSigned(IMessage $message, $micalg, $protocol)
    {
        if (strcasecmp($message->getContentType(), 'multipart/signed') !== 0) {
            $this->multipartHelper->enforceMime($message);
            $messagePart = $this->mimePartFactory->newInstance();
            $this->genericHelper->movePartContentAndChildren($message, $messagePart);
            $message->addChild($messagePart);
            $boundary = $this->multipartHelper->getUniqueBoundary('multipart/signed');
            $message->setRawHeader(
                HeaderConsts::CONTENT_TYPE,
                "multipart/signed;\r\n\tboundary=\"$boundary\";\r\n\tmicalg=\"$micalg\"; protocol=\"$protocol\""
            );
        }
        $this->overwrite8bitContentEncoding($message);
        $this->ensureHtmlPartFirstForSignedMessage($message);
        $this->setSignature($message, 'Empty');
    }

    /**
     * Sets the signature of the message to $body, creating a signature part if
     * one doesn't exist.
     *
     * @param IMessage $message
     * @param string $body
     */
    public function setSignature(IMessage $message, $body)
    {
        $signedPart = $message->getSignaturePart();
        if ($signedPart === null) {
            $signedPart = $this->mimePartFactory->newInstance();
            $message->addChild($signedPart);
        }
        $signedPart->setRawHeader(
            HeaderConsts::CONTENT_TYPE,
            $message->getHeaderParameter(HeaderConsts::CONTENT_TYPE, 'protocol')
        );
        $signedPart->setContent($body);
    }

    /**
     * Loops over parts of the message and sets the content-transfer-encoding
     * header to quoted-printable for text/* mime parts, and to base64
     * otherwise for parts that are '8bit' encoded.
     *
     * Used for multipart/signed messages which doesn't support 8bit transfer
     * encodings.
     *
     * @param IMessage $message
     */
    public function overwrite8bitContentEncoding(IMessage $message)
    {
        $parts = $message->getAllParts(function (IMessagePart $part) {
            return strcasecmp($part->getContentTransferEncoding(), '8bit') === 0;
        });
        foreach ($parts as $part) {
            $contentType = strtolower($part->getContentType());
            $part->setRawHeader(
                HeaderConsts::CONTENT_TRANSFER_ENCODING,
                ($contentType === 'text/plain' || $contentType === 'text/html') ?
                'quoted-printable' :
                'base64'
            );
        }
    }

    /**
     * Ensures a non-text part comes first in a signed multipart/alternative
     * message as some clients seem to prefer the first content part if the
     * client doesn't understand multipart/signed.
     *
     * @param IMessage $message
     */
    public function ensureHtmlPartFirstForSignedMessage(IMessage $message)
    {
        $alt = $message->getPartByMimeType('multipart/alternative');
        if ($alt !== null && $alt->getChildCount() > 0) {
            $cont = $this->multipartHelper->getContentPartContainerFromAlternative('text/html', $alt);
            $children = $alt->getChildParts();
            $pos = array_search($cont, $children, true);
            if ($pos !== false && $pos !== 0) {
                $alt->removePart($children[0]);
                $alt->addChild($children[0]);
            }
        }
    }

    /**
     * Returns a stream that can be used to read the content part of a signed
     * message, which can be used to sign an email or verify a signature.
     *
     * The method simply returns the stream for the first child.  No
     * verification of whether the message is in fact a signed message is
     * performed.
     *
     * Note that unlike getSignedMessageAsString, getSignedMessageStream doesn't
     * replace new lines.
     *
     * @param IMessage $message
     * @return \Psr\Http\Message\StreamInterface or null if the message doesn't
     *         have any children
     */
    public function getSignedMessageStream(IMessage $message)
    {
        $child = $message->getChild(0);
        if ($child !== null) {
            return $child->getStream();
        }
        return null;
    }

    /**
     * Returns a string containing the entire body (content) of a signed message
     * for verification or calculating a signature.
     *
     * Non-CRLF new lines are replaced to always be CRLF.
     *
     * @param IMessage $message
     * @return string or null if the message doesn't have any children
     */
    public function getSignedMessageAsString(IMessage $message)
    {
        $stream = $this->getSignedMessageStream($message);
        if ($stream !== null) {
            return preg_replace(
                '/\r\n|\r|\n/',
                "\r\n",
                $stream->getContents()
            );
        }
        return null;
    }
}

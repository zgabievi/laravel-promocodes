<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Header\Consumer;

/**
 * Parses a single ID from an ID header.  Begins consuming on a '<' char, and
 * ends on a '>' char.
 *
 * @author Zaahid Bateson
 */
class IdConsumer extends GenericConsumer
{
    /**
     * Overridden to return patterns matching the beginning part of an ID ('<'
     * and '>' chars).
     * 
     * @return string[] the patterns
     */
    public function getTokenSeparators()
    {
        return [ '\s+', '<', '>' ];
    }
    
    /**
     * Returns true for '>'.
     */
    protected function isEndToken($token)
    {
        return ($token === '>');
    }
    
    /**
     * Returns true for '<'.
     * 
     * @param string $token
     * @return boolean false
     */
    protected function isStartToken($token)
    {
        return ($token === '<');
    }

    /**
     * Returns null for whitespace, and LiteralPart for anything else.
     *
     * @param string $token the token
     * @param bool $isLiteral set to true if the token represents a literal -
     *        e.g. an escaped token
     * @return \ZBateson\MailMimeParser\Header\IHeaderPart|null the constructed
     *         header part or null if the token should be ignored
     */
    protected function getPartForToken($token, $isLiteral)
    {
        if (preg_match('/^\s+$/', $token)) {
            return null;
        }
        return $this->partFactory->newLiteralPart($token);
    }
}

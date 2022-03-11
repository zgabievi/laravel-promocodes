<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Header\Consumer;

/**
 * Parses the Address portion of an email address header, for an address part
 * that contains both a name and an email address, e.g. "name" <email@tld.com>.
 * 
 * The address portion found within the '<' and '>' chars may contain comments
 * and quoted portions.
 * 
 * @author Zaahid Bateson
 */
class AddressEmailConsumer extends AbstractConsumer
{
    /**
     * Returns the following as sub-consumers:
     *  - {@see AddressGroupConsumer}
     *  - {@see CommentConsumer}
     *  - {@see QuotedStringConsumer}
     * 
     * @return AbstractConsumer[] the sub-consumers
     */
    protected function getSubConsumers()
    {
        return [
            $this->consumerService->getCommentConsumer(),
            $this->consumerService->getQuotedStringConsumer(),
        ];
    }
    
    /**
     * Overridden to return patterns matching the beginning/end part of an
     * address in a name/address part ("<" and ">" chars).
     * 
     * @return string[] the patterns
     */
    public function getTokenSeparators()
    {
        return [ '<', '>' ];
    }
    
    /**
     * Returns true for the '>' char.
     * 
     * @param string $token
     * @return boolean false
     */
    protected function isEndToken($token)
    {
        return ($token === '>');
    }
    
    /**
     * Returns true for the '<' char.
     * 
     * @param string $token
     * @return boolean false
     */
    protected function isStartToken($token)
    {
        return ($token === '<');
    }

    /**
     * Returns a single AddressPart with its 'email' portion set, so an
     * AddressConsumer can identify it and create an AddressPart with both a
     * name and email set.
     * 
     * @param \ZBateson\MailMimeParser\Header\IHeaderPart[] $parts
     * @return \ZBateson\MailMimeParser\Header\IHeaderPart[]|array
     */
    protected function processParts(array $parts)
    {
        $strEmail = '';
        foreach ($parts as $p) {
            $strEmail .= $p->getValue();
        }
        return [ $this->partFactory->newAddressPart('', $strEmail) ];
    }
}

<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\AbstractConsumer;
use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;

/**
 * Abstract base class representing a mime email's header.
 *
 * The base class sets up the header's consumer for parsing, sets the name of
 * the header, and calls the consumer to parse the header's value.
 *
 * AbstractHeader::getConsumer is an abstract method that must be overridden to
 * return an appropriate Consumer\AbstractConsumer type.
 *
 * @author Zaahid Bateson
 */
abstract class AbstractHeader implements IHeader
{
    /**
     * @var string the name of the header
     */
    protected $name;

    /**
     * @var IHeaderPart[] the header's parts (as returned from the consumer)
     */
    protected $parts;

    /**
     * @var string the raw value
     */
    protected $rawValue;

    /**
     * Assigns the header's name and raw value, then calls getConsumer and
     * setParseHeaderValue to extract a parsed value.
     *
     * @param ConsumerService $consumerService For parsing the value.
     * @param string $name Name of the header.
     * @param string $value Value of the header.
     */
    public function __construct(ConsumerService $consumerService, $name, $value)
    {
        $this->name = $name;
        $this->rawValue = $value;

        $consumer = $this->getConsumer($consumerService);
        $this->setParseHeaderValue($consumer);
    }

    /**
     * Returns the header's Consumer
     *
     * @param ConsumerService $consumerService
     * @return AbstractConsumer
     */
    abstract protected function getConsumer(ConsumerService $consumerService);

    /**
     * Calls the consumer and assigns the parsed parts to member variables.
     *
     * The default implementation assigns the returned value to $this->part.
     *
     * @param AbstractConsumer $consumer
     */
    protected function setParseHeaderValue(AbstractConsumer $consumer)
    {
        $this->parts = $consumer($this->rawValue);
    }

    public function getParts()
    {
        return $this->parts;
    }

    public function getValue()
    {
        if (!empty($this->parts)) {
            return $this->parts[0]->getValue();
        }
        return null;
    }

    public function getRawValue()
    {
        return $this->rawValue;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return "{$this->name}: {$this->rawValue}";
    }
}

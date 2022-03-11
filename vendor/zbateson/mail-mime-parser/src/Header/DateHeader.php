<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\DatePart;
use DateTimeImmutable;

/**
 * Reads a DatePart value header in either RFC 2822 or RFC 822 format.
 * 
 * @author Zaahid Bateson
 */
class DateHeader extends AbstractHeader
{
    /**
     * Returns a DateConsumer.
     * 
     * @param ConsumerService $consumerService
     * @return Consumer\AbstractConsumer
     */
    protected function getConsumer(ConsumerService $consumerService)
    {
        return $consumerService->getDateConsumer();
    }
    
    /**
     * Convenience method returning the part's DateTime object, or null if the
     * date could not be parsed.
     * 
     * @return \DateTime|null The parsed DateTime object.
     */
    public function getDateTime()
    {
        if (!empty($this->parts) && $this->parts[0] instanceof DatePart) {
            return $this->parts[0]->getDateTime();
        }
        return null;
    }

    /**
     * Returns a DateTimeImmutable for the part's DateTime object, or null if
     * the date could not be parsed.
     *
     * @return DateTimeImmutable|null The parsed DateTimeImmutable object.
     */
    public function getDateTimeImmutable()
    {
        $dateTime = $this->getDateTime();
        if ($dateTime !== null) {
            return DateTimeImmutable::createFromMutable($dateTime);
        }
        return null;
    }
}

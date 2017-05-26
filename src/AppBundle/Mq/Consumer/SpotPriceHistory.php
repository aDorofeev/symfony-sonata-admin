<?php
/**
 * Created by PhpStorm.
 * User: adorofeev
 * Date: 5/23/17
 * Time: 4:13 PM
 */

namespace AppBundle\Mq\Consumer;

use AppBundle\Entity\RingRound;
use Aws\Result;
use Doctrine\Bundle\DoctrineBundle\Registry;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use AppBundle\Entity\SpotPriceHistory as SpotPriceHistoryEntity;

class SpotPriceHistory implements ConsumerInterface
{

    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function execute(AMQPMessage $msg)
    {
        $dataString = $msg->getBody();
        $data = unserialize($dataString);

        return $this->doExecute($data);
    }

    protected function dbQueryParams($awsResult)
    {
        $params = [];
        foreach ($awsResult as $key => $value) {
            $params[lcfirst($key)] = $value;
        }

        unset($params['SpotPrice']);

        return $params;
    }

    public function doExecute(Result $result)
    {
        $repo = $this->doctrine->getRepository(SpotPriceHistoryEntity::class);
        $em = $this->doctrine->getManagerForClass(SpotPriceHistoryEntity::class);

        $before = microtime(true);
        $rows = $result->get('SpotPriceHistory');
        foreach ($rows as $row) {
            $historyRecord = $repo->findOneBy($this->dbQueryParams($row));

            if (!$historyRecord) {
                $historyRecord = new SpotPriceHistoryEntity();
                $em->persist($historyRecord);
            }
            else {
                echo PHP_EOL . "    !!!DUPLICATE!!!   " . PHP_EOL . PHP_EOL;
            }

            $historyRecord->fromAwsResult($row);
        }

        $em->flush();
        $duration = microtime(true) - $before;
        echo $duration . PHP_EOL;

        return true;
    }

}
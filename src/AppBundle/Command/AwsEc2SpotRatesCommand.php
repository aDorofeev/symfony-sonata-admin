<?php

namespace AppBundle\Command;

use AppBundle\Entity\SpotPriceHistory;
use Aws\Ec2\Ec2Client;
use Doctrine\Bundle\DoctrineBundle\Registry;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AwsEc2SpotRatesCommand extends ContainerAwareCommand
{
    /** @var  OutputInterface */
    protected $output;

    /** @var  Registry */
    protected $doctrine;

    /** @var  Producer */
    protected $producer;

    /** @var Ec2Client[] */
    protected $ec2InstanceList = [];

    protected $defaultRegion = 'us-west-2';

    protected function configure()
    {
        $this
            ->setName('aws:ec2-spot-rates')
            ->setDescription('...')
            ->addArgument('region', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('dry', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function getEc2($regionName = null)
    {
        $regionName = $regionName ?: $this->defaultRegion;

        if (!isset($this->ec2InstanceList[$regionName])) {
            $this->ec2InstanceList[$regionName] = new Ec2Client([
                'region' => $regionName,
                'version' => 'latest',
                'credentials' => [
                    'key' => 'AKIAI4DFITYJCVCDTRSA',
                    'secret' => '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'
                ]
            ]);
        }

        return $this->ec2InstanceList[$regionName];
    }

    protected function query($queryParams, $regionName = null)
    {
        $cacheKey = md5(json_encode($queryParams));

        $cachePath = sprintf(
            '%s/cache/aws_describeSpotPriceHistory_%s_%s.serialize',
            $this->getContainer()->get('kernel')->getRootDir(),
            $regionName ?: $this->defaultRegion,
            $cacheKey
        );

        if (is_file($cachePath)) {
            $serialized = file_get_contents($cachePath);

            $result = unserialize($serialized);
        }
        else {
            for ($i = 1; $i<3; $i++) {
                try {
                    $result = $this->getEc2($regionName)->describeSpotPriceHistory($queryParams);
                    break;
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    $this->output->writeln('<error>' . $e->getResponse()->getBody()->getContents() . '</error>');
                    throw $e;
                } catch (\Aws\Ec2\Exception\Ec2Exception $e) {
                    $this->output->writeln('<error>' . $e->getMessage() . '</error>');
                    $this->output->writeln('<error>' . $e->getResponse()->getBody()->getContents() . '</error>');
                    continue;
                }
            }
            $serialized = serialize($result);

            file_put_contents($cachePath, $serialized);
        }

        return $result;
    }

    protected function iterate($queryParams, callable $callback = null)
    {
        $queryDuration = 0;
        $callbackDuration = 0;

        $stop = false;
        do
        {
//            $this->output->write('.', OutputInterface::VERBOSITY_NORMAL);

            $before = microtime(true);
            $result = $this->query($queryParams);
            $queryDuration += microtime(true) - $before;


            $nextToken = $result->get('NextToken');
            $queryParams['NextToken'] = $nextToken;

            if ($callback) {
                $before = microtime(true);
                $stop = $callback($result);
                $callbackDuration += microtime(true) - $before;
            }

            $this->output->writeln($nextToken, OutputInterface::VERBOSITY_VERY_VERBOSE);
            $this->output->writeln($queryDuration . "\t" . $callbackDuration);
            if ($stop) {
                $this->output->writeln('<info>Old data reached</info>');
                break;
            }
        }
        while ($nextToken);
        $this->output->writeln('<info>OK</info>');
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

    protected function processRegion($regionName)
    {
        $this->defaultRegion = $regionName;
        $this->output->writeln('<info>' . $this->defaultRegion . '</info>');
        if ($this->dryRun) {
            return;
        }

        $queryParams = [
//            'DryRun' => true || false,
            'StartTime' => new \DateTime('-3 days'),
//            'EndTime' => new \DateTime('now'),
//            'InstanceTypes' => [
//                'p2.16xlarge',
//                'x1.32xlarge',
//                'p2.8xlarge',
////                'd2.8xlarge',
//                'r4.16xlarge',
//                'm4.16xlarge',
////                'd2.4xlarge',
////                'r3.8xlarge',
////                'g2.8xlarge',
////                'i3.8xlarge',
////                'm4.10xlarge',
////                'r4.8xlarge',
////                'c3.8xlarge',
////                'c4.8xlarge',
////                'd2.2xlarge',
////                'r3.4xlarge',
////                'i3.4xlarge',
////                'r4.4xlarge',
////                'p2.xlarge',
//            ],
            'ProductDescriptions' => [
                'Linux/UNIX',
//                'SUSE Linux'
            ],
//            'Filters' => [
//                [
//                    'Name' => 'string',
//                    'Values' => ['string', ],
//                ],
//            ],
//            'AvailabilityZone' => 'string',
            'MaxResults' => 1000,
//            'NextToken' => 'string',
        ];


        $repo = $this->doctrine->getRepository(SpotPriceHistory::class);
        $em = $this->doctrine->getManagerForClass(SpotPriceHistory::class);
        $producer = $this->producer;

        $this->iterate($queryParams, function ($result) use ($repo, $em, $producer) {
//            $producer->publish(serialize($result));
//            return;

            /** @var array[] $rows */
            $rows = $result->get('SpotPriceHistory');
            foreach ($rows as $row) {
//                echo($row['Timestamp']->format('r') . "\t" . $row['SpotPrice'] . "\n");
//                continue;
                $historyRecord = $repo->findOneBy($this->dbQueryParams($row));

                if (!$historyRecord) {
                    $historyRecord = new SpotPriceHistory();
                    $em->persist($historyRecord);
                }
                else {
                    $em->flush();
                    return true;
                }

                $historyRecord->fromAwsResult($row);
            }

            $em->flush();
        });
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // SELECT h0.availability_zone, h0.instance_type, h0.timestamp, h0.spot_price FROM spot_price_history h0 JOIN (select h1.availability_zone, h1.instance_type, max(timestamp) as max_timestamp from spot_price_history h1 group by h1.availability_zone, h1.instance_type) as t ON h0.availability_zone = t.availability_zone AND h0.instance_type = t.instance_type AND h0.timestamp = t.max_timestamp;
        $this->output = $output;

        $regionName = $input->getArgument('region');
        $this->dryRun = $input->getOption('dry');

        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->producer = $this->getContainer()->get('old_sound_rabbit_mq.spot_price_history_producer');

        if ($regionName) {
            $this->processRegion($regionName);
        }
        else {
            $result = $this->getEc2()->describeRegions();
            foreach ($result->get('Regions') as $regionData) {
                $this->processRegion($regionData['RegionName']);
            }
        }

        $this->output->writeln('<info>Terminating</info>', OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

}

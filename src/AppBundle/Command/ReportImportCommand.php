<?php

namespace AppBundle\Command;

use AppBundle\Entity\Ec2Instance;
use AppBundle\Entity\Ec2InstanceHashrate;
use AppBundle\Entity\Ec2InstanceHashrateStats;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReportImportCommand extends ContainerAwareCommand
{

    /** @var  Registry */
    private $doctrine;

    /** @var  EntityManager */
    private $em;

    protected function configure()
    {
        $this
            ->setName('report:import')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * @param string $logFile
     * @param \DateTime $reportTime
     * @return Ec2Instance
     */
    protected function parseMetadata($logFile, \DateTime $reportTime)
    {
        $contents = file($logFile);

        $vars = [];
        foreach ($contents as $line) {
            $exploded = explode(': ', $line);
            if (count($exploded) != 2) {
                continue;
            }
            list($key, $value) = $exploded;
            $vars[$key] = $value;
        }
        $instance = $this->doctrine->getRepository('AppBundle:Ec2Instance')->findOneBy([
            'instanceId' => $vars['instance-id'],
        ]);

        if (!$instance) {
            $instance = new Ec2Instance();
            $instance->setInstanceId($vars['instance-id']);
            $this->em->persist($instance);
        }

        $instance
            ->setInstanceTypeName($vars['instance-type'])
            ->setPublicIpv4($vars['public-ipv4'])
            ->setPublicHostname($vars['public-hostname'])
            ->setAmiId($vars['ami-id'])
        ;
        $instance->setFirstReportAt($instance->getFirstReportAt() ? min($instance->getFirstReportAt(), $reportTime) : $reportTime);
        $instance->setLastReportAt($instance->getLastReportAt() ? max($instance->getLastReportAt(), $reportTime) : $reportTime);
        $instance->setUptimeSeconds($instance->getLastReportAt()->getTimestamp() -  $instance->getFirstReportAt()->getTimestamp());

        return $instance;
    }

    protected function parseCpuInfo($logFile, Ec2Instance $instance)
    {
        $contents = file($logFile);

        $vars = [];
        $processors = [];
        foreach ($contents as $line) {
            $exploded = explode(': ', $line);
            if (count($exploded) != 2) {
                continue;
            }
            list($key, $value) = $exploded;
            $key = trim($key);

            if (empty($vars[$key])) {
                $vars[$key] = $value;
            }
            if ($key === 'processor') {
                $processors[] = $value;
            }
        }

        $instance
            ->setCpuModel(trim($vars['model name']))
            ->setCpuMicrocode(trim($vars['microcode']))
            ->setCpuFreq($vars['cpu MHz'] * 1000000)
            ->setCpuCacheSize(preg_replace('/\D/', '', $vars['cache size']) * 1000)
            ->setCpuBogomips(round($vars['bogomips']))
            ->setCpuCoreCount(count($processors))
            ->setCpuAes(strpos($vars['flags'], 'aes') !== false)
        ;
    }

    protected function parseXmrStakCpuLog($logFile, Ec2Instance $instance, \DateTime $time)
    {
        $contents = file_get_contents($logFile);

        // get coin code
        $filename = basename($logFile);
        if (!preg_match('/(?<=_).+?(?=\.\w{3})/', $filename, $matches)) {
            return;
        }
        $coinCode = $matches[0];

        // get hashrate records
        if (!preg_match_all('/HASHRATE REPORT.+?-----------------------------------------------------/sm', $contents, $matches)) {
            return;
        }

        // calculate average
        $hashrates = [];
        $recordCount = 0;
        foreach($matches[0] as $report) {
            $lines = explode("\n", $report);

            $hashrates[$recordCount] = [];
            foreach ($lines as $line) {
                $cells = explode(' | ', $line);
                if (isset($cells[6]) && is_numeric($cells[6])) {
                    $coreId = preg_replace('/\D/', '', $cells[0]);
                    $hashrate60 = $cells[6];
                    $hashrates[$recordCount][$coreId] = isset($hashrates[$recordCount][$coreId]) ? $hashrates[$recordCount][$coreId] + $hashrate60 : $hashrate60;
                }
            }
            $recordCount++;
        }

        $totalRate = 0;
        foreach ($hashrates as $record) {
            $recordSum = 0;
            foreach ($record as $coreRecord) {
                $recordSum += $coreRecord;
            }
            $totalRate += $recordSum;
        }

        $totalAvgHashrate = $totalRate/count($hashrates);
        $this->saveHashrate($instance, $coinCode, $time, $totalAvgHashrate);
    }

    protected function addHashrateStats(Ec2InstanceHashrate $hashrateRecord)
    {
        $instance = $hashrateRecord->getEc2Instance();
        $coinCode = $hashrateRecord->getCoinCode();

        $hashrateStatsRecord = $this->doctrine->getRepository('AppBundle:Ec2InstanceHashrateStats')->findOneBy([
            'ec2Instance' => $instance,
            'coinCode' => $coinCode,
        ]);

        if (!$hashrateStatsRecord) {
            $hashrateStatsRecord = new Ec2InstanceHashrateStats();
            $hashrateStatsRecord
                ->setEc2Instance($instance)
                ->setCoinCode($coinCode)
                ->setHashrateSum(0)
                ->setRecordCount(0)
            ;
            $this->em->persist($hashrateStatsRecord);
        }
        $hashrateStatsRecord->setHashrateSum($hashrateStatsRecord->getHashrateSum() + $hashrateRecord->getHashrate());
        $hashrateStatsRecord->setRecordCount($hashrateStatsRecord->getRecordCount() + 1);
        $hashrateStatsRecord->setHashrateAvg($hashrateStatsRecord->getHashrateSum() / $hashrateStatsRecord->getRecordCount());
    }

    protected function saveHashrate($instance, $coinCode, \DateTime $time, $hashrate)
    {
        $hashrateRecord = $this->doctrine->getRepository('AppBundle:Ec2InstanceHashrate')->findOneBy([
            'ec2Instance' => $instance,
            'coinCode' => $coinCode,
            'time' => $time
        ]);

        if (!$hashrateRecord) {
            $hashrateRecord = new Ec2InstanceHashrate();
            $hashrateRecord
                ->setEc2Instance($instance)
                ->setCoinCode($coinCode)
                ->setTime($time)
            ;
            $hashrateRecord->setHashrate($hashrate);
            $this->em->persist($hashrateRecord);
            $this->addHashrateStats($hashrateRecord);
        }
        $hashrateRecord->setHashrate($hashrate);
    }


    protected function parseClaymoreLog($logFile, Ec2Instance $instance, \DateTime $time)
    {
        $contents = file_get_contents($logFile);

        preg_match_all('/(ETH|DCR) - Total Speed:\s+(.+?)\s+Mh\/s/', $contents, $matches);

        $coinHashrateSum = [];
        foreach ($matches[1] as $id => $coinCode) {
            $coinCode = strtolower($coinCode);
            $hashrate = $matches[2][$id];
            $coinHashrateSum[$coinCode] = isset($coinHashrateSum[$coinCode]) ? $coinHashrateSum[$coinCode] + $hashrate : $hashrate;
        }
        foreach ($coinHashrateSum as $coinCode => $hashrateSum) {
            $hashrateAvg = $hashrateSum/count($matches[1]);
            $this->saveHashrate($instance, $coinCode, $time, $hashrateAvg);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $this->output = $output;

        if ($input->getOption('option')) {
            // ...
        }

        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->em = $this->doctrine->getManagerForClass(Ec2Instance::class);

        $rootDir = $dataPath = $this->getContainer()->get('kernel')->getRootDir();
        $reportDir = $rootDir . '/data/report';
        $processedDir = $rootDir . '/data/report_processed';
        $failedDir = $rootDir . '/data/report_failed';
        @mkdir($processedDir);
        @mkdir($failedDir);

        foreach (glob($reportDir . '/*.tar.gz') as $reportFile) {
            $reportFilename = basename($reportFile);
            dump($reportFilename);

            // calculate report time
            preg_match('/\d{10,}(?=.tar.gz)/', $reportFilename, $matches);
            $unixtime = $matches[0];
            $reportTime = new \DateTime('@'.$unixtime);

            // extract
            $tmpDir = $reportFile . '.extracted';
            @mkdir($tmpDir);
            $extractCommand = sprintf('tar zxf %s -C %s',
                $reportFile,
                $tmpDir
            );
//            dump($extractCommand);

            passthru($extractCommand, $returnVar);
            if ($returnVar) {
                dump($returnVar);
                rename($reportFile, $failedDir . '/' . $reportFilename);
                passthru('rm -rf ' . $tmpDir);
                continue;
                die;
            }

            // list files
            $command = 'find ' . $tmpDir . '/ -type f';
//            dump($command);

            $output = [];
            exec($command, $output);

            $instance = null;
            foreach ($output as $logFile) {
                $filename = basename($logFile);
                if ($filename === 'ec2metadata.out') {
                    if (!is_file($logFile)) {
                        dump($command, $output, $logFile, $tmpDir);
                        die;
                    }

                    $instance = $this->parseMetadata($logFile, $reportTime);
                }
            }

            foreach ($output as $logFile) {
                $filename = basename($logFile);
                if ($filename === 'ec2metadata.out') {
//                    $this->parseMetadata($logFile);
                }
                elseif ($filename === 'cpuinfo.out') {
                    $this->parseCpuInfo($logFile, $instance);
                }
                elseif (preg_match('/^xmr-stack-cpu_.+\.log$/', $filename)) {
                    $this->parseXmrStakCpuLog($logFile, $instance, $reportTime);
                }
                elseif ($filename === 'claymore_eth_plus_dcr.log') {
                    $this->parseClaymoreLog($logFile, $instance, $reportTime);
                }
                elseif (preg_match('/\.err?$/', $logFile)) {
                    continue;
                }
                elseif ($filename === '.gitkeep') {
                    continue;
                }
                else {
                    dump($filename);
                    die;
                }
            }

            $this->em->flush();
            rename($reportFile, $processedDir . '/' . $reportFilename);
            passthru('rm -rf ' . $tmpDir);
        }

        $this->em->flush();

        $this->output->writeln('Command result.');
    }

}

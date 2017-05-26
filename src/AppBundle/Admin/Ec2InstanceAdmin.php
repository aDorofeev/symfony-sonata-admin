<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class Ec2InstanceAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('instanceId')
            ->add('instanceTypeName')
            ->add('amiId')
            ->add('publicIpv4')
            ->add('publicHostname')
            ->add('cpuModel')
            ->add('cpuCoreCount')
            ->add('cpuMicrocode')
            ->add('cpuFreq')
            ->add('cpuCacheSize')
            ->add('cpuBogomips')
            ->add('cpuAes')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('instanceId')
            ->add('instanceTypeName', null, ['label' => 'Type'])
//            ->add('amiId')
            ->add('publicIpv4')
//            ->add('publicHostname')
            ->add('cpuModel')
            ->add('cpuCoreCount', null, ['label' => 'Cores'])
//            ->add('firstReportAt')
            ->add('lastReportAt')
            ->add('uptimeSeconds', null, ['label' => 'Uptime'])
//            ->add('cpuMicrocode')
//            ->add('cpuFreq')
//            ->add('cpuCacheSize')
//            ->add('cpuBogomips')
//            ->add('cpuAes')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
//                    'edit' => array(),
//                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('instanceId')
            ->add('instanceTypeName')
            ->add('amiId')
            ->add('publicIpv4')
            ->add('publicHostname')
            ->add('cpuModel')
            ->add('cpuCoreCount')
            ->add('cpuMicrocode')
            ->add('cpuFreq')
            ->add('cpuCacheSize')
            ->add('cpuBogomips')
            ->add('cpuAes')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('instanceId')
            ->add('instanceTypeName')
            ->add('amiId')
            ->add('publicIpv4')
            ->add('publicHostname')
            ->add('cpuModel')
            ->add('cpuCoreCount')
            ->add('cpuMicrocode')
            ->add('cpuFreq')
            ->add('cpuCacheSize')
            ->add('cpuBogomips')
            ->add('cpuAes')
            ->add('firstReportAt')
            ->add('lastReportAt')
            ->add('uptimeSeconds')
            ->add('hashrateStats', null, ['associated_property' => 'getView'])
        ;
    }
}

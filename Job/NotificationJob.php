<?php

namespace WobbleCode\NotificationBundle\Job;

use Instasent\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Finder\Finder;

class NotificationJob extends ContainerAwareJob
{
    /**
     * @var string The queue name
     */
    public $queue = 'notification';

    public function run($args)
    {
        $this->getContainer()->get($args["channel_service"])->processNotification($args);
    }

    protected function createKernel()
    {
        $finder = new Finder();
        $finder->name('AppKernel.php')->depth(0)->in($this->args['kernel.root_dir']);
        $results = iterator_to_array($finder);
        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return new $class(
            isset($this->args['kernel.environment']) ? $this->args['kernel.environment'] : 'dev',
            isset($this->args['kernel.debug']) ? $this->args['kernel.debug'] : true
        );
    }

    public function getName()
    {
        return 'notification';
    }
}

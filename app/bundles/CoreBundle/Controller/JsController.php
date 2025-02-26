<?php

namespace Mautic\CoreBundle\Controller;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Symfony\Component\HttpFoundation\Response;

class JsController extends CommonController
{
    public function indexAction(): Response
    {
        // Don't store a visitor with this request
        defined('MAUTIC_NON_TRACKABLE_REQUEST') || define('MAUTIC_NON_TRACKABLE_REQUEST', 1);

        $dispatcher = $this->dispatcher;
        $debug      = $this->factory->getKernel()->isDebug();
        $event      = new BuildJsEvent($this->getJsHeader(), $debug);

        if ($dispatcher->hasListeners(CoreEvents::BUILD_MAUTIC_JS)) {
            $dispatcher->dispatch($event, CoreEvents::BUILD_MAUTIC_JS);
        }

        return new Response($event->getJs(), 200, ['Content-Type' => 'application/javascript']);
    }

    /**
     * Build a JS header for the Mautic embedded JS.
     *
     * @return string
     */
    protected function getJsHeader()
    {
        $year = date('Y');

        return <<<JS
/**
 * @package     MauticJS
 * @copyright   {$year} Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
JS;
    }
}

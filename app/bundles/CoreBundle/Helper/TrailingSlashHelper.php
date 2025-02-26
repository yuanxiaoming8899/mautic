<?php

namespace Mautic\CoreBundle\Helper;

use Symfony\Component\HttpFoundation\Request;

class TrailingSlashHelper
{
    private \Mautic\CoreBundle\Helper\CoreParametersHelper $coreParametersHelper;

    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @return string
     */
    public function getSafeRedirectUrl(Request $request)
    {
        $siteUrl  = $this->coreParametersHelper->get('site_url');
        $pathInfo = substr($request->getPathInfo(), 0, -1);

        return $siteUrl.$pathInfo;
    }
}

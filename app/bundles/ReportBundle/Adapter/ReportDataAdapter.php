<?php

namespace Mautic\ReportBundle\Adapter;

use Mautic\ReportBundle\Crate\ReportDataResult;
use Mautic\ReportBundle\Entity\Report;
use Mautic\ReportBundle\Model\ReportExportOptions;
use Mautic\ReportBundle\Model\ReportModel;

class ReportDataAdapter
{
    private \Mautic\ReportBundle\Model\ReportModel $reportModel;

    public function __construct(ReportModel $reportModel)
    {
        $this->reportModel = $reportModel;
    }

    public function getReportData(Report $report, ReportExportOptions $reportExportOptions): ReportDataResult
    {
        $options                    = [];
        $options['paginate']        = true;
        $options['limit']           = $reportExportOptions->getBatchSize();
        $options['ignoreGraphData'] = true;
        $options['page']            = $reportExportOptions->getPage();
        $options['dateTo']          = $reportExportOptions->getDateTo();
        $options['dateFrom']        = $reportExportOptions->getDateFrom();

        $data = $this->reportModel->getReportData($report, null, $options);

        return new ReportDataResult($data);
    }
}

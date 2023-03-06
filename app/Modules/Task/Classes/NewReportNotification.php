<?php

namespace App\Modules\Task\Classes;

use App\Modules\Task\Models\Report;

/**
 * дествие после добавление отчета
 */
class NewReportNotification
{
    private $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function run()
    {
        $task = $this->report->task;
        $viewers = $task->viewReport;
        foreach ($viewers as $viewer) {
            if ($viewer->user_id != $this->report->user_id) {
                $viewer->show = $viewer->show + 1;
                $viewer->save();
            }
        }
    }

}

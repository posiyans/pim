<?php

namespace App\Modules\Task\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['protokol'] = $this->protokol->number;
        $last_report = $this->report->last();
        if ($last_report) {
            $last_report->files;
        }
        $data['last_report'] = $last_report;
        $data['view_report'] = $this->viewReport;
        $data['partition'] = $this->partition;
        $data['execution'] = $this->getPercentComplete();
        return $data;
    }
}
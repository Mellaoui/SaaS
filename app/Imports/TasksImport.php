<?php

namespace App\Imports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\ToModel;

class TasksImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Task([
            'title' => $row['title'],
            'description' => $row['description'],
            'start_date' => $row['start_date'],
            'progress' =>  $row['progress'],
            'priority' =>  $row['priority'],
            'media' => $row['media'],
        ]);
    }
}

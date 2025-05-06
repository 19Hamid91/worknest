<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    public function createProject(array $data)
    {
        return Project::create([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'],
        ]);
    }

    public function updateProject(Project $project, array $data)
    {
        $project->update([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'],
        ]);

        return $project;
    }
}

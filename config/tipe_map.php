<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Job Type Mapping
    |--------------------------------------------------------------------------
    |
    | Single source of truth for job type labels and CSS classes used across
    | all controllers and views (HomeController, JobController, AdminController,
    | RecruiterController).
    |
    */

    'full-time'   => ['class' => '',        'label' => 'Full Time'],
    'part-time'   => ['class' => 'parttime', 'label' => 'Part Time'],
    'remote'      => ['class' => 'remote',   'label' => 'Remote'],
    'hybrid'      => ['class' => 'hybrid',   'label' => 'Hybrid'],
    'contract'    => ['class' => 'contract', 'label' => 'Contract'],
    'partnership' => ['class' => 'partner',  'label' => 'Partnership'],
];

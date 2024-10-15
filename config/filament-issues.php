<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Organizations
    |--------------------------------------------------------------------------
    |
    | List of organizations to search for issues.
    |
    */
    'orgs' => [],

    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    |
    | List of repositories to search for issues.
    |
    */
    'repos' => [],

    /*
    |--------------------------------------------------------------------------
    | Labels
    |--------------------------------------------------------------------------
    |
    | List of labels to search for issues.
    |
    */
    'labels' => [
        'bug',
        'help wanted',
        'enhancement',
        'documentation'
    ],

    /*
    |--------------------------------------------------------------------------
    | Reactions
    |--------------------------------------------------------------------------
    |
    | List of reactions to use when reacting to issues.
    |
    */
    'reactions' => [
        '+1' => '👍', // \u{1F44D}
        '-1' => '👎', // \u{1F44E}
        'laugh' => '😄', // \u{1F604}
        'hooray' => '🎉', // \u{1F389}
        'confused' => '😕', // \u{1F615}
        'heart' => '❤️', // \u{2764}
        'rocket' => '🚀', // \u{1F680}
        'eyes' => '👀', // \u{1F440}
    ],
];

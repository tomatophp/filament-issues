<?php

return [
    "group" => "Content",
    "title" => "Issues",
    "single" => "Issue",
    "columns" => [
        "title" => "Title",
        "by" => "By",
        "opened" => "Opened",
        "in-repository" => "In Repository",
        "labels" => "Labels",
        "comments" => "Comments",
        "is_public" => "Is Public?",
        "is_trend" => "Is Trending?",
        "repo" => "Repository",
        "isPullRequest" => "Is Pull Request?",
    ],
    "actions" => [
        "refresh" => [
            "label" =>  "Refresh Issues",
            "title" => "Issues Refreshed",
            "body" => "Issues have been refreshed in the background successfully."
        ],
        "clean" => [
            "label" => "Clean Issues",
            "title" => "Issues Cleaned",
            "body" => "Issues have been cleaned successfully."
        ]
    ]
];

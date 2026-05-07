<?php

namespace App\Services\Sources\Enums;

enum SourceDriverType: string
{
    case GRAPHQL = 'graphql';
    case HTML_PARSER = 'html_parser';
}

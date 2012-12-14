<?php
namespace SMGregsList\Frontend;
/**
 * a proxy, used for selecting templates
 */
class SearchModal
{
    public $searchresults;
    function __construct(array $results)
    {
        $this->searchresults = $results;
    }
}
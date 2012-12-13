<?php
namespace SMGregsList\Frontend;
use SMGregsList\Player;
/**
 * a proxy, used for selecting templates
 */
class SearchResults
{
    public $searchform;
    public $searchresults;
    function __construct(Player $search, array $results)
    {
        $this->searchform = $search;
        $this->searchresults = $results;
    }
}
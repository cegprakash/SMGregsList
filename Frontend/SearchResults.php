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
    public $manager;
    public $code;
    function __construct(Player $search, array $results, $manager, $code)
    {
        $this->searchform = $search;
        $this->searchresults = $results;
        $this->manager = $manager;
        $this->code = $code;
    }
}
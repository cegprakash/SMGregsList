<?php
namespace SMGregsList\Frontend;
use SMGregsList\Player, SMGregsList\SavedSearches;
/**
 * a proxy, used for selecting templates
 */
class SearchResults
{
    public $savedSearches;
    public $searchform;
    public $searchresults;
    public $manager;
    public $code;
    function __construct(SavedSearches $searches, Player $search, array $results, $manager, $code)
    {
        $this->savedSearches = $searches;
        $this->searchform = $search;
        $this->searchresults = $results;
        $this->manager = $manager;
        $this->code = $code;
    }
}
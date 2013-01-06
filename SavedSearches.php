<?php
namespace SMGregsList;
class SavedSearches
{
    public $ids = array();
    function __construct(array $ids)
    {
        foreach ($ids as $id) {
            if ($id instanceof SavedSearch) {
                $this->ids[] = $id;
            } else {
                $this->ids[] = new SavedSearch($id);
            }
        }
    }
}
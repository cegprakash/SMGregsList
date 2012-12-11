<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\Frontend, SMGregsList\SearchPlayer, SMGregsList\Player;
class HTML extends Messager implements Frontend
{
    protected $template;
    protected $searchresults = array();
    function __construct(HTMLController $controller = null)
    {
        if (null === $controller) {
            $controller = new HTMLController;
        }
        $this->addDependency($controller);
        $this->template = new \PEAR2\Templates\Savant\Main(array(
            'template_path' => realpath(__DIR__ . '/../templates'),
            'escape' => 'htmlentities',
        ));
    }

    function listMessages()
    {
        return parent::listMessages(array('ready', 'searchResult'));
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            // display html output
            $this->discoverSearch();
            $this->displayMainPage();
            $this->displaySearchResults($this->searchresults);
        } elseif ($message == 'searchResult') {
            if (!is_array($content)) {
                throw new \Exception('internal Error: results of search is not an array');
            }
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('internal Error: one of the results of search is not a Player object');
                }
            }
            $this->searchresults = $content;
        }
    }

    function displayMainPage()
    {
        echo $this->template->render(new SearchPlayer);
    }

    function discoverSearch()
    {
        $this->searchresults = array();
        $this->broadcast('detectSearch');
    }

    function displaySearchResults(array $results)
    {
        if (!count($results)) {
            echo "<p><strong>No results</strong></p>\n";
        } else {
            echo $this->template->render($this->searchresults, 'SMGregsList/searchresults.tpl.php');
        }
    }
}
<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\Frontend, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer;
class HTML extends Messager implements Frontend
{
    protected $template;
    protected $searchresults = array();
    protected $searchfor;
    protected $verifyphase = false;
    protected $confirmphase = false;
    protected $deleted = false;
    function __construct(HTMLController $controller = null)
    {
        if (null === $controller) {
            $controller = new HTMLController;
        }
        $this->searchfor = new SearchPlayer;
        $this->sellplayer = new SellPlayer;
        $this->addDependency($controller);
        $this->template = new \PEAR2\Templates\Savant\Main(array(
            'template_path' => realpath(__DIR__ . '/../templates'),
            'escape' => 'htmlentities',
        ));
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array('ready', 'searchResult', 'search', 'playerAdded', 'sellDetected',
                                          'verify', 'confirm', 'playerRemoved'));
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            // display html output
            $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
            if (strpos($phpSelf, 'sell.php')) {
                $this->discoverSell();
                $this->displaySellPage();
            } else {
                $this->discoverSearch();
                $this->displayMainPage();
                $this->displaySearchResults($this->searchresults);
            }
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
        } elseif ($message == 'search') {
            $this->searchfor = $content;
        } elseif ($message == 'playerAdded' || $message == 'sellDetected') {
            $this->sellplayer = $content;
        } elseif ($message == 'verify') {
            $this->verifyphase = true;
            $this->confirmphase = false;
        } elseif ($message == 'playerRemoved') {
            $this->sellplayer = $content;
            $this->deleted = true;
        } elseif ($message == 'confirm') {
            $this->confirmphase = true;
            $this->verifyphase = false;
        }
    }

    function displayMainPage()
    {
        echo $this->template->render($this->searchfor);
    }

    function displaySellPage()
    {
        if ($this->verifyphase) {
            echo $this->template->render($this->sellplayer, 'SMGregsList/VerifySellPlayer.tpl.php');
        } elseif ($this->confirmphase) {
            echo $this->template->render($this->sellplayer, 'SMGregsList/PlayerListed.tpl.php');
            echo $this->template->render($this->sellplayer);
        } elseif ($this->deleted) {
            echo $this->template->render($this->sellplayer, 'SMGregsList/PlayerDeleted.tpl.php');
            echo $this->template->render($this->sellplayer);
        } else {
            echo $this->template->render($this->sellplayer, 'SMGregsList/SellPlayer.tpl.php');
        }
    }

    function discoverSearch()
    {
        $this->searchresults = array();
        $this->broadcast('detectSearch');
    }

    function discoverSell()
    {
        $this->broadcast('detectSell');
    }

    function displaySearchResults(array $results)
    {?><table border="1">
    <tr><td>id</td><td>Position</td><td>Average</td><td>Age</td><td>Experience</td><td>Forecast</td><td>Progression</td></tr>
<?php
        echo $this->template->render($this->searchresults, 'SMGregsList/searchresults.tpl.php');
        ?></table><?php
    }
}